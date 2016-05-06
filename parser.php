<?php

if( (count($argv) != 5 && $argv[1] != '-f') || (count($argv) != 6 && $argv[1] == '-f') ){
  echo 'USAGE -> php parser.php [-f] FileName NewsPaperTitle YYYY-MM-DD PageNumber [-f to write the MongoId in the file id.temp]';
}else{

  if($argv[1]=='-f'){
    $index = 1;
  }else{
    $index = 0;
  }
  //External data 
  $nameFile = $argv[1+$index];
  $providedTitlePage = $argv[2+$index];
  $datePage = $argv[3+$index];
  $number = intval($argv[4+$index]);

  // select a database
  require 'vendor/autoload.php';

  $client = new MongoClient();
     
  $db = $client->test;   
  $PageCollection = $db->Pages;
  $ArticleCollection = $db->Articles;


  //Data of the page
  $idPage = new MongoId();

  $dataXML = simplexml_load_file($nameFile.'.res.xml');
  $titlePage = $dataXML->pageRecoTitre->titreJournal;
  $titlePageText = '';

  $titlePageText = $providedTitlePage; 


  //insertion into database
  $dataPage = array(
      '_id' => $idPage,
      'Title' => $titlePageText,
      'Date' => $datePage,
      'NumberPage' => $number,
      'Articles' => array(),
      'Picture' => $nameFile
    );

  $PageCollection->insert($dataPage);

  //Articles table for the page data
  $pageArticles = array();


  //////////////////////////////////////////
  //Data of the articles, insertion into the database and updating the page data
  //////////////////////////////////////////
    foreach($dataXML->xpath('//article') as $article){
      $idArticle = new MongoId();

      //Title data
      $titleArticle = '';
      $coordTitle = array(PHP_INT_MAX, PHP_INT_MAX, 0, 0);

      foreach($article->titreA->xpath('.//lineText') as $line){

        $coordTitle[0] = min($coordTitle[0], intval($line->ccx->coorDeb->children('http://irisa.fr/intuidoc/lp')->num[0]));
        $coordTitle[1] = min($coordTitle[1], intval($line->ccx->coorDeb->children('http://irisa.fr/intuidoc/lp')->num[1]));
        $coordTitle[2] = max($coordTitle[2], intval($line->ccx->coorFin->children('http://irisa.fr/intuidoc/lp')->num[0]));
        $coordTitle[3] = max($coordTitle[3], intval($line->ccx->coorFin->children('http://irisa.fr/intuidoc/lp')->num[1]));
      }

      foreach($article->titreA->xpath('.//mot') as $titleWords){
        $titleArticle = $titleArticle.$titleWords->children('http://irisa.fr/intuidoc/lp')->str.' ';
      }
      
      

      //Content data
      $words = array();

      $PictureKeys = array();

      //Words and paragraphs data
      $x1 = array();
      $y1 = array();

      $x2 = array();
      $y2 = array();



      foreach($article->children('http://irisa.fr/intuidoc/lp')->list as $content){
        //For the pictures keys
        foreach($content->xpath('.//parIllustration') as $illustration){

          $keyText = '';
          foreach($illustration->xpath('.//mot') as $Keys){
              $keyText = $keyText.$Keys->children('http://irisa.fr/intuidoc/lp')->str.' ';
            }
          array_push($PictureKeys, $keyText);
        }

        //For the article text
        foreach($content->xpath('.//lineText') as $parag){

            $coordx1 = $parag->ccx->coorDeb->children('http://irisa.fr/intuidoc/lp')->num[0];
            $coordy1 = $parag->ccx->coorDeb->children('http://irisa.fr/intuidoc/lp')->num[1];
            $coordx2 = $parag->ccx->coorFin->children('http://irisa.fr/intuidoc/lp')->num[0];
            $coordy2 = $parag->ccx->coorFin->children('http://irisa.fr/intuidoc/lp')->num[1];

            //For article content
            $paragText = '';
            $list = $parag->children('http://irisa.fr/intuidoc/lp')->list;

            foreach($parag->xpath('.//mot') as $Words){
              $paragText = $paragText.$Words->children('http://irisa.fr/intuidoc/lp')->str.' ';
            }

            array_push($words, array('Coord' => array(intval($coordx1), intval($coordy1), intval($coordx2), intval($coordy2)), 'Word' => $paragText));
          }

        foreach($content->xpath('.//parText') as $parag){

          $coordx1 = $parag->ccx->coorDeb->children('http://irisa.fr/intuidoc/lp')->num[0];
          $coordy1 = $parag->ccx->coorDeb->children('http://irisa.fr/intuidoc/lp')->num[1];
          $coordx2 = $parag->ccx->coorFin->children('http://irisa.fr/intuidoc/lp')->num[0];
          $coordy2 = $parag->ccx->coorFin->children('http://irisa.fr/intuidoc/lp')->num[1];

          //For article coordinates
          $i = 0;
          for($i ; $i<count($x1) ; $i++){
            if( abs(intval($coordx1) - intval($x1[$i]))/(intval($x2[$i]) - intval($x1[$i])) <= 0.6 && abs(intval($coordy1) - intval($y2[$i]))/(intval($y2[$i]) - intval($y1[$i])) <= 1 ) break;
          }

          if($i != count($x1)){
            if(intval($coordx1) < $x1[$i]){
              $x1[$i] = intval($coordx1);
            }
            if(intval($coordy1) < $y1[$i]){
              $y1[$i] = intval($coordy1);
            }
            if(intval($coordx2) > $x2[$i]){
              $x2[$i] = intval($coordx2);
            }
            if(intval($coordy2) > $y2[$i]){
              $y2[$i] = intval($coordy2);
            }
          }else{
            array_push($x1, intval($coordx1));
            array_push($y1, intval($coordy1));
            array_push($x2, intval($coordx2));
            array_push($y2, intval($coordy2));
          }

        }
      }

      $coord = array();
      for($i=0; $i<count($x1) ; $i++){
        array_push($coord, array($x1[$i], $y1[$i], $x2[$i], $y2[$i]));
      }

      $dataArticle = array(
          '_id' => $idArticle,
          'Title' => $titleArticle,
          'IdPage' => $idPage,
          'TitleNewsPaper' => $titlePageText,
          'Date' => $datePage,
          'TitleCoord' => $coordTitle,
          'Coord' => $coord,
          'Reviews' => array(),
          'Words' => $words,
          'Views' => 0
        );

      //Légendes des images non ajoutées
      array_push($pageArticles, array('IdArticle' => $idArticle, 'Title' => $titleArticle.'', 'TitleCoord' => $coordTitle, 'Coord' => $coord, 'PictureKeys' => $PictureKeys));

      $ArticleCollection->insert($dataArticle);

    }


      //////////////////////////////////////////
      //Data of the articles which don't have title, insertion into the database and updating the page data
      //////////////////////////////////////////
    foreach($dataXML->xpath('//articleSansTitre') as $article){
      $idArticle = new MongoId();

      //Title data
      $titleArticle = '(Sans titre)';
      $coordTitle = array();
      

      //Content data
      $words = array();

      $PictureKeys = array();

      //Words and paragraphs data
      $x1 = array();
      $y1 = array();

      $x2 = array();
      $y2 = array();

      //For the pictures keys
      foreach($article->xpath('.//parIllustration') as $illustration){

        $keyText = '';
        foreach($illustration->xpath('.//mot') as $Keys){
            $keyText = $keyText.$Keys->children('http://irisa.fr/intuidoc/lp')->str.' ';
        }

        array_push($PictureKeys, $keyText);
      }

      foreach($article->xpath('.//lineText') as $parag){

        $coordx1 = $parag->ccx->coorDeb->children('http://irisa.fr/intuidoc/lp')->num[0];
        $coordy1 = $parag->ccx->coorDeb->children('http://irisa.fr/intuidoc/lp')->num[1];
        $coordx2 = $parag->ccx->coorFin->children('http://irisa.fr/intuidoc/lp')->num[0];
        $coordy2 = $parag->ccx->coorFin->children('http://irisa.fr/intuidoc/lp')->num[1];

        //For article content
        $paragText = '';

        foreach($parag->xpath('.//mot') as $Words){
          $paragText = $paragText.$Words->children('http://irisa.fr/intuidoc/lp')->str.' ';
        }

        array_push($words, array('Coord' => array(intval($coordx1), intval($coordy1), intval($coordx2), intval($coordy2)), 'Word' => $paragText));
      }

      foreach($article->xpath('.//parText') as $parag){

        $coordx1 = $parag->ccx->coorDeb->children('http://irisa.fr/intuidoc/lp')->num[0];
        $coordy1 = $parag->ccx->coorDeb->children('http://irisa.fr/intuidoc/lp')->num[1];
        $coordx2 = $parag->ccx->coorFin->children('http://irisa.fr/intuidoc/lp')->num[0];
        $coordy2 = $parag->ccx->coorFin->children('http://irisa.fr/intuidoc/lp')->num[1];

        //For article coordinates
        $i = 0;
        for($i ; $i<count($x1) ; $i++){
          if( abs(intval($coordx1) - intval($x1[$i]))/(intval($x2[$i]) - intval($x1[$i])) <= 0.6 && abs(intval($coordy1) - intval($y2[$i]))/(intval($y2[$i]) - intval($y1[$i])) <= 1 ) break;
        }

        if($i != count($x1)){
          if(intval($coordx1) < $x1[$i]){
              $x1[$i] = intval($coordx1);
          }
          if(intval($coordy1) < $y1[$i]){
            $y1[$i] = intval($coordy1);
          }
          if(intval($coordx2) > $x2[$i]){
            $x2[$i] = intval($coordx2);
          }
          if(intval($coordy2) > $y2[$i]){
            $y2[$i] = intval($coordy2);
          }

        }else{
          array_push($x1, intval($coordx1));
          array_push($y1, intval($coordy1));
          array_push($x2, intval($coordx2));
          array_push($y2, intval($coordy2));
        }

      }

      $coord = array();
      for($i=0; $i<count($x1) ; $i++){
        array_push($coord, array($x1[$i], $y1[$i], $x2[$i], $y2[$i]));
      }

      $dataArticle = array(
          '_id' => $idArticle,
          'Title' => $titleArticle,
          'IdPage' => $idPage,
          'TitleNewsPaper' => $titlePageText,
          'Date' => $datePage,
          'TitleCoord' => $coordTitle,
          'Coord' => $coord,
          'Reviews' => array(),
          'Words' => $words,
          'Views' => 0
        );

      //Légendes des images non ajoutées
      array_push($pageArticles, array('IdArticle' => $idArticle, 'Title' => $titleArticle.'', 'Coord' => $coord, 'PictureKeys' => $PictureKeys));

      $ArticleCollection->insert($dataArticle);

    }

      

  $PageCollection->update(array('_id' => $idPage), array('$set' => array('Articles' => $pageArticles)));

  if($argv[1]=='-f'){
    file_put_contents('id.temp', $idPage."\n", FILE_APPEND);
  }
}

?>