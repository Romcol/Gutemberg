(function($) {
    'use strict';

    if (!$.version || $.version.major < 2) {
        throw new Error('This version of OpenSeadragonSelection requires OpenSeadragon version 2.0.0+');
    }

    $.Viewer.prototype.selection = function(options) {
        if (!this.selectionInstance || options) {
            options = options || {};
            options.viewer = this;
            this.selectionInstance = new $.Selection(options);
        }
        return this.selectionInstance;
    };


    /**
    * @class Selection
    * @classdesc Provides functionality for selecting part of an image
    * @memberof OpenSeadragon
    * @param {Object} options
    */
    $.Selection = function ( options ) {

        $.extend( true, this, {
            // internal state properties
            viewer:                  null,
            isSelecting:             false,
            buttonActiveImg:         false,
            rectDone:                true,

            // options
            element:                 null,
            toggleButton:            null,
            showSelectionControl:    true,
            showConfirmDenyButtons:  true,
            styleConfirmDenyButtons: true,
            returnPixelCoordinates:  true,
            keyboardShortcut:        'c',
            rect:                    null,
            startRotated:            false, // useful for rotated crops
            startRotatedHeight:      0.1,
            restrictToImage:         false,
            onSelection:             null,
            prefixUrl:               null,
            navImages:               {
                selection: {
                    REST:   'selection_rest.png',
                    GROUP:  'selection_grouphover.png',
                    HOVER:  'selection_hover.png',
                    DOWN:   'selection_pressed.png'
                },
                selectionConfirm: {
                    REST:   'selection_confirm_rest.png',
                    GROUP:  'selection_confirm_grouphover.png',
                    HOVER:  'selection_confirm_hover.png',
                    DOWN:   'selection_confirm_pressed.png'
                },
                selectionCancel: {
                    REST:   'selection_cancel_rest.png',
                    GROUP:  'selection_cancel_grouphover.png',
                    HOVER:  'selection_cancel_hover.png',
                    DOWN:   'selection_cancel_pressed.png'
                },
            }
        }, options );

        $.extend( true, this.navImages, this.viewer.navImages );

        if (!this.element) {
            this.element = $.makeNeutralElement('div');
            this.element.style.background = 'rgba(0, 0, 0, 0.1)';
            this.element.className        = 'selection-box';
        }
        this.borders = this.borders || [];
        var handle;
        var corners = [];
        for (var i = 0; i < 4; i++) {
            if (!this.borders[i]) {
                this.borders[i]                  = $.makeNeutralElement('div');
                this.borders[i].className        = 'border-' + i;
                this.borders[i].style.position   = 'absolute';
                this.borders[i].style.width      = '1px';
                this.borders[i].style.height     = '1px';
                this.borders[i].style.background = '#fff';
            }

            handle                  = $.makeNeutralElement('div');
            handle.className        = 'border-' + i + '-handle';
            handle.style.position   = 'absolute';
            handle.style.top        = '50%';
            handle.style.left       = '50%';
            handle.style.width      = '6px';
            handle.style.height     = '6px';
            handle.style.margin     = '-4px 0 0 -4px';
            handle.style.background = '#000';
            handle.style.border     = '1px solid #ccc';
            new $.MouseTracker({
                element:     this.borders[i],
                dragHandler: onBorderDrag.bind(this, i),
            });

            corners[i]                  = $.makeNeutralElement('div');
            corners[i].className        = 'corner-' + i + '-handle';
            corners[i].style.position   = 'absolute';
            corners[i].style.width      = '6px';
            corners[i].style.height     = '6px';
            corners[i].style.background = '#000';
            corners[i].style.border     = '1px solid #ccc';
            new $.MouseTracker({
                element:     corners[i],
                dragHandler: onBorderDrag.bind(this, i + 0.5),
            });

            this.borders[i].appendChild(handle);
            this.element.appendChild(this.borders[i]);
            // defer corners, so they are appended last
            setTimeout(this.element.appendChild.bind(this.element, corners[i]), 0);
        }
        this.borders[0].style.top = 0;
        this.borders[0].style.width = '100%';
        this.borders[1].style.right = 0;
        this.borders[1].style.height = '100%';
        this.borders[2].style.bottom = 0;
        this.borders[2].style.width = '100%';
        this.borders[3].style.left = 0;
        this.borders[3].style.height = '100%';
        corners[0].style.top = '-3px';
        corners[0].style.left = '-3px';
        corners[1].style.top = '-3px';
        corners[1].style.right = '-3px';
        corners[2].style.bottom = '-3px';
        corners[2].style.right = '-3px';
        corners[3].style.bottom = '-3px';
        corners[3].style.left = '-3px';

        if (!this.overlay) {
            this.overlay = new $.SelectionOverlay(this.element, this.rect || new $.SelectionRect());
        }

        this.innerTracker = new $.MouseTracker({
            element:            this.element,
            clickTimeThreshold: this.viewer.clickTimeThreshold,
            clickDistThreshold: this.viewer.clickDistThreshold,
            dragHandler:        $.delegate( this, onInsideDrag ),
            dragEndHandler:     $.delegate( this, onInsideDragEnd ),
            // keyHandler:         $.delegate( this, onKeyPress ),
            clickHandler:       $.delegate( this, onClick ),
            // scrollHandler:      $.delegate( this.viewer, this.viewer.innerTracker.scrollHandler ),
            // pinchHandler:       $.delegate( this.viewer, this.viewer.innerTracker.pinchHandler ),
        });

        this.outerTracker = new $.MouseTracker({
            element:            this.viewer.drawer.canvas,
            clickTimeThreshold: this.viewer.clickTimeThreshold,
            clickDistThreshold: this.viewer.clickDistThreshold,
            dragHandler:        $.delegate( this, onOutsideDrag ),
            dragEndHandler:     $.delegate( this, onOutsideDragEnd ),
            clickHandler:       $.delegate( this, onClick ),
            startDisabled:      !this.isSelecting,
        });

        if (this.keyboardShortcut) {
            $.addEvent(
                this.viewer.container,
                'keypress',
                $.delegate(this, onKeyPress),
                false
            );
        }

        var prefix = this.prefixUrl || this.viewer.prefixUrl || '';
        var useGroup = this.viewer.buttons && this.viewer.buttons.buttons;
        var anyButton = useGroup ? this.viewer.buttons.buttons[0] : null;
        var onFocusHandler = anyButton ? anyButton.onFocus : null;
        var onBlurHandler = anyButton ? anyButton.onBlur : null;
        if (this.showSelectionControl) {
            this.toggleButton = new $.Button({
                element:    this.toggleButton ? $.getElement( this.toggleButton ) : null,
                clickTimeThreshold: this.viewer.clickTimeThreshold,
                clickDistThreshold: this.viewer.clickDistThreshold,
                tooltip:    $.getString('Tooltips.SelectionToggle') || 'Toggle selection',
                srcRest:    prefix + this.navImages.selection.REST,
                srcGroup:   prefix + this.navImages.selection.GROUP,
                srcHover:   prefix + this.navImages.selection.HOVER,
                srcDown:    prefix + this.navImages.selection.DOWN,
                onRelease:  this.toggleState.bind( this ),
                onFocus:    onFocusHandler,
                onBlur:     onBlurHandler
            });
            if (useGroup) {
                this.viewer.buttons.buttons.push(this.toggleButton);
                this.viewer.buttons.element.appendChild(this.toggleButton.element);
            }
            if (this.toggleButton.imgDown) {
                this.buttonActiveImg = this.toggleButton.imgDown.cloneNode(true);
                this.toggleButton.element.appendChild(this.buttonActiveImg);
            }
        }
        if (this.showConfirmDenyButtons) {
            this.confirmButton = new $.Button({
                element:    this.confirmButton ? $.getElement( this.confirmButton ) : null,
                clickTimeThreshold: this.viewer.clickTimeThreshold,
                clickDistThreshold: this.viewer.clickDistThreshold,
                tooltip:    $.getString('Tooltips.SelectionConfirm') || 'Confirm selection',
                srcRest:    prefix + this.navImages.selectionConfirm.REST,
                srcGroup:   prefix + this.navImages.selectionConfirm.GROUP,
                srcHover:   prefix + this.navImages.selectionConfirm.HOVER,
                srcDown:    prefix + this.navImages.selectionConfirm.DOWN,
                onRelease:  this.confirm.bind( this ),
                onFocus:    onFocusHandler,
                onBlur:     onBlurHandler
            });
            var confirm = this.confirmButton.element;
            this.element.appendChild(confirm);

            this.cancelButton = new $.Button({
                element:    this.cancelButton ? $.getElement( this.cancelButton ) : null,
                clickTimeThreshold: this.viewer.clickTimeThreshold,
                clickDistThreshold: this.viewer.clickDistThreshold,
                tooltip:    $.getString('Tooltips.SelectionConfirm') || 'Cancel selection',
                srcRest:    prefix + this.navImages.selectionCancel.REST,
                srcGroup:   prefix + this.navImages.selectionCancel.GROUP,
                srcHover:   prefix + this.navImages.selectionCancel.HOVER,
                srcDown:    prefix + this.navImages.selectionCancel.DOWN,
                onRelease:  this.cancel.bind( this ),
                onFocus:    onFocusHandler,
                onBlur:     onBlurHandler
            });
            var cancel = this.cancelButton.element;
            this.element.appendChild(cancel);

            if (this.styleConfirmDenyButtons) {
                confirm.style.position = 'absolute';
                confirm.style.top = '50%';
                confirm.style.left = '50%';
                confirm.style.transform = 'translate(-100%, -50%)';

                cancel.style.position = 'absolute';
                cancel.style.top = '50%';
                cancel.style.left = '50%';
                cancel.style.transform = 'translate(0, -50%)';
            }
        }

        this.viewer.addHandler('selection', this.onSelection);

        this.viewer.addHandler('open', this.draw.bind(this));
        this.viewer.addHandler('animation', this.draw.bind(this));
        this.viewer.addHandler('resize', this.draw.bind(this));
        this.viewer.addHandler('rotate', this.draw.bind(this));
    };

    $.extend( $.Selection.prototype, $.ControlDock.prototype, /** @lends OpenSeadragon.Selection.prototype */{

        toggleState: function() {
            return this.setState(!this.isSelecting);
        },

        setState: function(enabled) {
            this.isSelecting = enabled;
            // this.viewer.innerTracker.setTracking(!enabled);
            this.outerTracker.setTracking(enabled);
            enabled ? this.draw() : this.undraw();
            if (this.buttonActiveImg) {
                this.buttonActiveImg.style.visibility = enabled ? 'visible' : 'hidden';
            }
            this.viewer.raiseEvent('selection_toggle', {enabled: enabled});
            return this;
        },

        enable: function() {
            return this.setState(true);
        },

        disable: function() {
            return this.setState(false);
        },

        draw: function() {
            if (this.rect) {
                this.overlay.update(this.rect.normalize());
                this.overlay.drawHTML(this.viewer.drawer.container, this.viewer.viewport);
            }
            return this;
        },

        undraw: function() {
            this.overlay.destroy();
            this.rect = null;
            return this;
        },

        confirm: function() {
            if (this.rect) {
                var result = this.rect.normalize();
                if (this.returnPixelCoordinates) {
                    var real = this.viewer.viewport.viewportToImageRectangle(result);
                    real = $.SelectionRect.fromRect(real).round();
                    real.rotation = result.rotation;
                    result = real;
                }
                this.viewer.raiseEvent('selection', result);
                this.undraw();
            }
            return this;
        },

        cancel: function() {
            this.viewer.raiseEvent('selection_cancel', false);
            return this.undraw();
        },
    });

    function onOutsideDrag(e) {
        var delta = this.viewer.viewport.deltaPointsFromPixels(e.delta, true);
        var end = this.viewer.viewport.pointFromPixel(e.position, true);
        var start = new $.Point(end.x - delta.x, end.y - delta.y);
        if (!this.rect) {
            if (this.restrictToImage) {
                if (!pointIsInImage(start)) {
                    return;
                }
                restrictVector(delta, end);
            }
            if (this.startRotated) {
                this.rotatedStartPoint = start;
                this.rect = getPrerotatedRect(start, end, this.startRotatedHeight);
            } else {
                this.rect = new $.SelectionRect(start.x, start.y, delta.x, delta.y);
            }
            this.rectDone = false;
        } else {
            var oldRect;
            if (this.restrictToImage) {
                oldRect = this.rect.clone();
            }
            if (this.rectDone) {
                // rotate
                var angle1 = this.rect.getAngleFromCenter(start);
                var angle2 = this.rect.getAngleFromCenter(end);
                this.rect.rotation = (this.rect.rotation + angle1 - angle2) % Math.PI;
            } else {
                if (this.startRotated) {
                    this.rect = getPrerotatedRect(this.rotatedStartPoint, end, this.startRotatedHeight);
                } else {
                    this.rect.width += delta.x;
                    this.rect.height += delta.y;
                }
            }
            if (this.restrictToImage && !this.rect.fitsIn(new $.Rect(0, 0, 1, 1))) {
                this.rect = oldRect;
            }
        }
        this.draw();
    }

    function onOutsideDragEnd() {
        this.rectDone = true;
    }

    function onClick() {
        this.viewer.canvas.focus();
    }

    function onInsideDrag(e) {
        $.addClass(this.element, 'dragging');
        var delta = this.viewer.viewport.deltaPointsFromPixels(e.delta, true);
        this.rect.x += delta.x;
        this.rect.y += delta.y;
        if (this.restrictToImage && !this.rect.fitsIn(new $.Rect(0, 0, 1, 1))) {
            this.rect.x -= delta.x;
            this.rect.y -= delta.y;
        }
        this.draw();
    }

    function onInsideDragEnd() {
        $.removeClass(this.element, 'dragging');
    }

    function onBorderDrag(border, e) {
        var delta = e.delta;
        var rotation = this.rect.getDegreeRotation();
        var center;
        var oldRect = this.restrictToImage ? this.rect.clone() : null;
        if (rotation !== 0) {
            // adjust vector
            delta = delta.rotate(-1 * rotation, new $.Point(0, 0));
            center = this.rect.getCenter();
        }
        delta = this.viewer.viewport.deltaPointsFromPixels(delta, true);
        switch (border) {
            case 0:
                this.rect.y += delta.y;
                this.rect.height -= delta.y;
                break;
            case 1:
                this.rect.width += delta.x;
                break;
            case 2:
                this.rect.height += delta.y;
                break;
            case 3:
                this.rect.x += delta.x;
                this.rect.width -= delta.x;
                break;
            case 0.5:
                this.rect.y += delta.y;
                this.rect.height -= delta.y;
                this.rect.x += delta.x;
                this.rect.width -= delta.x;
                break;
            case 1.5:
                this.rect.y += delta.y;
                this.rect.height -= delta.y;
                this.rect.width += delta.x;
                break;
            case 2.5:
                this.rect.width += delta.x;
                this.rect.height += delta.y;
                break;
            case 3.5:
                this.rect.height += delta.y;
                this.rect.x += delta.x;
                this.rect.width -= delta.x;
                break;
        }
        if (rotation !== 0) {
            // calc center deviation
            var newCenter = this.rect.getCenter();
            // rotate new center around old
            var target = newCenter.rotate(rotation, center);
            // adjust new center
            delta = target.minus(newCenter);
            this.rect.x += delta.x;
            this.rect.y += delta.y;
        }
        if (this.restrictToImage && !this.rect.fitsIn(new $.Rect(0, 0, 1, 1))) {
            this.rect = oldRect;
        }
        this.draw();
    }

    function onKeyPress(e) {
        var key = e.keyCode ? e.keyCode : e.charCode;
        if (key === 13) {
            this.confirm();
        } else if (String.fromCharCode(key) === this.keyboardShortcut) {
            this.toggleState();
        }
    }

    function getPrerotatedRect(start, end, height) {
        if (start.x > end.x) {
            // always draw left to right
            var x = start;
            start = end;
            end = x;
        }
        var delta = end.minus(start);
        var dist = start.distanceTo(end);
        var angle = -1 * Math.atan2(delta.x, delta.y) + (Math.PI / 2);
        var center = new $.Point(
            delta.x / 2 + start.x,
            delta.y / 2 + start.y
        );
        var rect = new $.SelectionRect(
            center.x - (dist / 2),
            center.y - (height / 2),
            dist,
            height,
            angle
        );
        var heightModDelta = new $.Point(0, height);
        heightModDelta = heightModDelta.rotate(rect.getDegreeRotation(), new $.Point(0, 0));
        rect.x += heightModDelta.x / 2;
        rect.y += heightModDelta.y / 2;
        return rect;
    }

    function pointIsInImage(point) {
        return point.x >= 0 && point.x <= 1 && point.y >= 0 && point.y <= 1;
    }

    function restrictVector(delta, end) {
        var start;
        for (var prop in {x: 0, y: 0}) {
            start = end[prop] - delta[prop];
            if (start < 1 && start > 0) {
                if (end[prop] > 1) {
                    delta[prop] -= end[prop] - 1;
                    end[prop] = 1;
                } else if (end[prop] < 0) {
                    delta[prop] -= end[prop];
                    end[prop] = 0;
                }
            }
        }
    }

})(OpenSeadragon);