<?php
  session_start();

  $letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
  $WON = false;

  if(isset($_GET['start'])){
    session_destroy();
    session_start();
  }

  //varibale for testing
  $guess = "BOOKMARK";
  $maxLetters = strlen($guess) - 1;

  //random words
  $words = [
   "AVATEACH", "ANGOSHT", "DIVAR", "SOORAKH", "OMID",
    "GAME", "PHP"
  ];

  $pictureParts = ["1","2","3","4","5","6"];

  function getCurrentPicture($part){
    return "./img/part_". $part . ".png";
  }

  function getParts(){
    global $pictureParts;
    return isset($_SESSION["parts"]) ? $_SESSION["parts"] : $pictureParts;
  }

  function addParts(){
    $parts = getParts();
    array_shift($parts);
    $_SESSION["parts"] = $parts;
  }

  function getCurrentPart(){
    $parts = getParts();
    return $parts[0];
  }

  // get the current word
  function getCurrentWord(){
    global $words;
    if(!isset($_SESSION["word"]) && empty($_SESSION["word"])){
      $key = array_rand($words);
      $_SESSION["word"] = $words[$key];
    }
    return $_SESSION["word"];
  }

  // check if pressed letter is correct
  function isLetterCorrect($letter){
    $word = getCurrentWord();
    $max = strlen($word) - 1;
    for($i = 0; $i <= $max; $i++){
      if($letter == $word[$i]){
        return true;
      }
    }
    return false;
  }
  // get user response
  function getCurrentResponses(){
    return isset($_SESSION["responses"]) ? $_SESSION["responses"] : [];
  }

  function addResponse($letter){
    $responses = getCurrentResponses();
    array_push($responses, $letter);
    $_SESSION["responses"] = $responses;
  }

  function isWorldCorrect(){
    $guess = getCurrentWord();
    $responses = getCurrentResponses();
    $max = strlen($guess) - 1;
    for($i=0; $i <= $max; $i++){
      if(!in_array($guess[$i], $responses)){
        return false;
      }
    }
    return true;
  }

  //check if parts complete
  function isBodyComplete(){
    $parts = getParts();
    if(count($parts) <= 1){
      return true;
    }
    return false;
  }

  //manage game session
  //is game complete
  function gameComplete(){
    return isset($_SESSION["gamecomplete"]) ? $_SESSION["gamecomplete"] : false;
  }
  //set game as complete
  function markGameAsComplete(){
    $_SESSION["gamecomplete"] = true;
  }


  //detect when key is pressed
  if(isset($_GET['kp'])){
    $currentPressedkey = isset($_GET['kp']) ? $_GET['kp'] : null;
    //if the key press is correct
    if($currentPressedkey
    && isLetterCorrect($currentPressedkey)
    && !isBodyComplete()
    && !gameComplete()
    ){
      addResponse($currentPressedkey);
      if(isWorldCorrect()){
        $WON = true; //game complete
        markGameAsComplete();
      }
    }else{
      if(!isBodyComplete()){
        addParts();
        if(isBodyComplete()){
            markGameAsComplete(); //lost condition
        }
      }else{
        markGameAsComplete(); //lost condition
      }

    }
  }

?>
<html>
  <head>
    <meta charset="utf-8">
    <title>دیوار انگشت کن</title>
  </head>
  <body style="background: red">
    <!-- main -->
    <div style="margin: 0 auto; background: #dddddd; width:900px; height:800px; padding:5px; border-radius:3px;">
      <!-- display img -->
      <div style="display:inline-block; width: 500px; background:#fff;">
        <img style="width:80%; display:inline-block;" src="<?php echo getCurrentPicture(getCurrentPart()); ?>"/>

        <!-- indicate game status -->
        <?php if(gameComplete()): ?>
          <h1>GAME COMPLETE</h1>
        <?php endif; ?>
        <?php if($WON && gameComplete()): ?>
          <p style="color: darkgreen; font-size: 25px;">you won!</p>
        <?php elseif(!$WON && gameComplete()): ?>
          <p style="color: darkgreen; font-size: 25px;">you lost!</p>
        <?php endif; ?>

      </div>

      <div style="float:right; display:inline; vertical-align:top;">
        <h1>دیوار انگشت کن</h1>
        <div style="display:inline-block;">
          <form method="get">
            <?php
              $max = strlen($letters) - 1;
              for($i = 0; $i <= $max; $i++){
                echo "<button type='submit' name='kp' value='". $letters[$i] . "'>". $letters[$i] . "</button>";
                if($i == 12){
                  echo '<br>';
                }
              }
            ?>
            <br><br>
            <!-- button to restart -->
            <button type="submit" name="start">restart game</button>
          </form>
        </div>
    </div>
      <div style="margin-top:20px; padding:15px; background: lightseagreen; color: #fcf8e3">
        <!-- display current guesses -->
        <?php
          $guess = getCurrentWord();
          $maxLetters = strlen($guess) - 1;
          for($j = 0; $j <= $maxLetters; $j++): $l = getCurrentWord()[$j]; ?>
          <?php if(in_array($l, getCurrentResponses())): ?>
            <span style="font-size: 35px; border-bottom: 3px solid #000; margin-right: 5px;"><?php echo $l ?></span>
          <?php else: ?>
            <span style="font-size: 35px; border-bottom: 3px solid #000; margin-right: 5px;">&nbsp;&nbsp;&nbsp;</span>
          <?php endif; ?>
        <?php endfor; ?>
      </div>
  </body>
</html>
