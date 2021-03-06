<?

session_start();

// abraham's twitteroauth library
// https://github.com/abraham/twitteroauth
require_once('twitteroauth/twitteroauth.php');

// store consumer key/secret and access token/secret away from prying eyes
// config.php is gitignored; confer config-sample.php
require_once('config.php');

date_default_timezone_set('America/New_York');

/*

Relevant Twitter documentation:

Get tokens from dev.twitter.com:
https://dev.twitter.com/docs/auth/tokens-devtwittercom

Single-user OAuth:
https://dev.twitter.com/docs/auth/oauth/single-user-with-examples#php

REST API 1.1, GET lists/members:
https://dev.twitter.com/docs/api/1.1/get/lists/members

*/

// create oauth object using keys stored in config.php, which is gitignored
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET); 

// twitter lists from which to fetch users
// should be collectively exhaustive, but needn't be mutually exclusive (though it helps api efficiency)
$lists = array(
	array(
		"user" => "bowdoincollege",
		"list" => "alumni-students-1"),
	array(
		"user" => "bowdoincollege",
		"list" => "alumni-students-2"),
	array(
		"user" => "tophtucker",
		"list" => "bowdoin-alums")
	);

// users from all lists will be dumped in here and then ordered and de-duped
$users = array();

// fetch all lists and merge into users array
// this is pretty api-intensive; you can easily get rate-limited if you refresh a few times within a few minutes
foreach($lists as $list) {
	$next_cursor = -1;
	$i = 0;
	// cursor through each page of the list
	while($next_cursor !== 0) {
		$page = $connection->get("lists/members.json?slug=".$list['list']."&owner_screen_name=".$list['user']."&cursor=".$next_cursor);
		$next_cursor = $page->next_cursor;
		$users = array_merge($users, $page->users);
		$i++;
	}
}

// compare follower counts to sort highest to lowest
function cmp_followers($a, $b)
{
	if ($a->followers_count == $b->followers_count) {
        return 0;
    }
    return ($a->followers_count > $b->followers_count) ? -1 : 1;
}

// sort users from highest follower count to lowest
usort($users, "cmp_followers");

// remove duplicates
$users = array_unique($users, SORT_REGULAR);

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Bowdoin alumni* sorted by number of Twitter followers</title>
	
	<link rel="shortcut icon" href="polarchomp.png">
	
	<link rel="stylesheet" href="style.css">
	
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	
	<!-- Bootstrap CDN: Latest compiled and minified CSS -->
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.1/css/bootstrap.min.css">
	<!-- Bootstrap CDN: Latest compiled and minified JavaScript -->
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.1/js/bootstrap.min.js"></script>
	
</head>
<body>

<!-- Google Analytics -->
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-45488007-1', 'toph.me');
  ga('send', 'pageview');

</script>

<!-- Facebook JS -->
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=342498109177441";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<img src="polarchase.png" style="margin-top:-20px;" align="right">

<h1>Bowdoin alumni* sorted by number of Twitter followers</h1>
<p>*Rather, people who have attended or currently attend. List mostly assembled by <a href="https://twitter.com/bowdoincollege" target="new">@BowdoinCollege</a>. Site assembled by <a href="https://twitter.com/tophtucker" target="new">@tophtucker</a>. <br/><small>Last updated <?=date('m/d/Y h:i:s a')?>.</small></p>
<div class="fb-like" data-href="http://toph.me/bowdointweeps" data-colorscheme="light" data-layout="button_count" data-action="like" data-show-faces="false" data-send="false"></div>
<a href="https://twitter.com/share" class="twitter-share-button" data-lang="en">Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>

<hr>

<table class="table-hover">
	<tr class="user" style="border-bottom:1px solid #eee">
		<td class="user-rank"><a href="https://www.facebook.com/reed1960" target="new">0</a></td>
		<td class="user-pic"><a href="https://www.facebook.com/reed1960" target="new"><img src="reedhastings.jpg" width="48" height="48"></a></td>
		<td class="user-name" style="vertical-align: top;"><a href="https://www.facebook.com/reed1960" target="new"><small>HONORARY</small><br/>Reed Hastings <img src="verified-fb.png" class="verified" width="15" height="15"></a></td>
		<td class="user-handle"><a href="https://www.facebook.com/reed1960" target="new">—</a></td>
		<td class="user-desc"><a href="https://www.facebook.com/reed1960" target="new">Co-founder and CEO of Netflix <img src="f.png" width="16" height="16" align="right" style="margin-right:-16px; margin-top:2px; -webkit-filter: grayscale(100%);-moz-filter: grayscale(100%);filter: grayscale(100%);"></a></td>
		<td class="user-followers"><a href="https://www.facebook.com/reed1960" target="new">293,668</a></td>
		<td class="user-search-o"><a href="http://bowdoinorient.com/search?q=Reed Hastings" title="Search for Reed Hastings on The Bowdoin Orient" target="new"><img src="o.png" width="16" height="16"></a></td>
		<td class="user-search-g"><a href="http://google.com/search?q=Reed Hastings" title="Search for Reed Hastings on Google" target="new"><img src="g.png" width="16" height="16"></a></td>
		<td class="user-search-w"><a href="http://en.wikipedia.org/w/index.php?title=Special:Search&search=Reed Hastings" title="Search for Reed Hastings on Wikipedia" target="new"><img src="w.png" width="16" height="16"></a></td>
	</tr>
<? foreach($users as $n => $user): ?>
	<tr class="user">
		<td class="user-rank"><a href="https://twitter.com/<?= $user->screen_name ?>" target="new"><?= $n+1 ?></a></td>
		<td class="user-pic"><a href="https://twitter.com/<?= $user->screen_name ?>" target="new"><img src="<?= $user->profile_image_url ?>" width="48" height="48"></a></td>
		<td class="user-name"><a href="https://twitter.com/<?= $user->screen_name ?>" target="new"><?= $user->name ?><? if($user->verified): ?> <img src="verified.png" class="verified" width="15" height="15"><? endif; ?></a></td>
		<td class="user-handle"><a href="https://twitter.com/<?= $user->screen_name ?>" target="new">@<?= $user->screen_name ?></a></td>
		<td class="user-desc"><a href="https://twitter.com/<?= $user->screen_name ?>" target="new"><?= $user->description ?></a></td>
		<td class="user-followers"><a href="https://twitter.com/<?= $user->screen_name ?>" target="new"><?= number_format($user->followers_count) ?></a></td>
		<td class="user-search-o"><a href="http://bowdoinorient.com/search?q=<?= htmlspecialchars($user->name) ?>" title="Search for <?= htmlspecialchars($user->name) ?> on The Bowdoin Orient" target="new"><img src="o.png" width="16" height="16"></a></td>
		<td class="user-search-g"><a href="http://google.com/search?q=<?= htmlspecialchars($user->name) ?>" title="Search for <?= htmlspecialchars($user->name) ?> on Google" target="new"><img src="g.png" width="16" height="16"></a></td>
		<td class="user-search-w"><a href="http://en.wikipedia.org/w/index.php?title=Special:Search&search=<?= htmlspecialchars($user->name) ?>" title="Search for <?= htmlspecialchars($user->name) ?> on Wikipedia" target="new"><img src="w.png" width="16" height="16"></a></td>
	</tr>
<? endforeach; ?>
</table>

<hr>

<blockquote class="pull-right">
  <p>“This list makes me want to kill myself.”</p>
  <small>Jay Caspian Kang, <a href="https://twitter.com/jaycaspiankang/status/397864888837476352" target="new"><cite title="Source Title">on Twitter</cite></a></small>
</blockquote>

<blockquote>
  <p>“All is vanity, nothing is fair.” </p>
  <small>Georgina Howell, in <cite title="Source Title"><em>The Times Sunday Magazine</em>, 1986</cite></a></small>
</blockquote>

<!-- 

"Then I saw in my dream, that when they were got out of the wilderness,
they presently saw a town before them, and the name of that town is
Vanity; and at the town there is a fair kept, called Vanity Fair: it is
kept all the year long... these pilgrims set very light by all their
wares; they cared not so much as to look upon them; and if they called
upon them to buy, they would put their fingers in their ears, and cry,
Turn away mine eyes from beholding vanity, and look upwards, signifying
that their trade and traffic was in heaven." 

http://www.gutenberg.org/files/131/131-h/131-h.htm

-->

</body>
</html>
