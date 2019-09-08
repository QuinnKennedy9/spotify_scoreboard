<?php
include('connect.php');
require 'vendor/autoload.php';

$session = new SpotifyWebAPI\Session(
    '8e53f1a2b45b4ca19746bec4fcd2afe4',
    '2cd5612a0ab041e0888d2aa3557cddb2',
    'http://www.quinnedydesigns.com/scoreboard'
);

$userCheck = "SELECT * FROM user_info ORDER BY stream_count DESC";
$userCheckQuery = mysqli_query($link, $userCheck);
$users = array();
		while($row = mysqli_fetch_assoc($userCheckQuery)) {
			$users[] = $row;
        }


$api = new SpotifyWebAPI\SpotifyWebAPI();

if (isset($_GET['code'])) {
    $userFound = false;
    $session->requestAccessToken($_GET['code']);
    $api->setAccessToken($session->getAccessToken());

    $me = $api->me();
    $enteredName = $me->display_name;

    for ($x = 0; $x < count($users); $x++) {
        if(strtolower($users[$x]['user_display']) == $enteredName){
            $userFound = true;
            $scoreCount = $users[$x]['stream_count'];
            $oldRecentTracks = $users[$x]['most_recent'];
        }
    } 


    $tracks = $api->getMyRecentTracks([
        'limit' => 30,
    ]);
    $recentTracks = [];
    foreach ($tracks->items as $track) {
        $track = $track->track;
        
        $trackname = $track->name;
        array_push($recentTracks, $trackname);       
    }
    $recentTrackString =  implode(' ', $recentTracks);
    if($recentTrackString === $oldRecentTracks){
    }else{
        
        foreach ($tracks->items as $track) {
            $track = $track->track;
            
            if($track->id == '4SSnFejRGlZikf02HLewEF'){
                $scoreCount = $scoreCount + 1;
            }
            
            
        }
    }
    

        
    
    if(!$userFound){
        $addUser = "INSERT INTO user_info (user_display,stream_count,most_recent) VALUES ('{$enteredName}','{$scoreCount}', {$recentTrackString}) ";
        $addUserQuery = mysqli_query($link, $addUser);
    }else{
    $updateScore = "UPDATE user_info SET stream_count='{$scoreCount}', most_recent='{$recentTrackString}' WHERE user_display='{$enteredName}'";
        $updateScore = mysqli_query($link, $updateScore);
    }



} else {
    $options = [
        'scope' => [
            'user-read-email',
            'user-read-recently-played',
            'user-library-read',
            'user-library-modify',
        ],
    ];

    header('Location: ' . $session->getAuthorizeUrl($options));
    die();
}

?>

<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Streaming Leader Board</title>
<link rel="stylesheet" href="main.css">
</head>
<body>

    <h1>Streaming Contest Leaderboard</h1>
    <p></p>
    <div class='option-container'>
        <?php
            for ($c = 0; $c < count($users); $c++) {
                echo "<div class='option'><h2>Username:   {$users[$c]['user_display']}<span>Score:{$users[$c]['stream_count']}</span></h2></div>";
            }
        ?>
    </div>

</body>
</html>
