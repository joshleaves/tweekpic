# tWeekPic #

tWeekPic was a web app inspired by [Graham Grafx](http://grahamgrafx.com/) that would change your [Twitter](http://www.twitter.com/) profile picture for you every day of the week.

Today, the app is no longer online (and I don't plan to put it back online), so I'm just releasing the source code in the wild.

## From the original app ##
tWeekPic - It's Morphin' Time! Every day of the Week!

tWeekPic is a Twitter application that allows you to set up different profile pictures for each day of the week. Once you've set up your schedule, our application will automatically use the Twitter API to update your profile picture according to your wishes.

## History ##
I wrote tWeekPic in november of 2011. I was on "holiday" and spending a lot of time on [Twitter](http://www.twitter.com/) and [HackerNews](https://news.ycombinator.com/item?id=3284306). I wanted to build a web app after a full year of learning C (and coding my own RayCaster, CoreWar, Shell,...). All I had was knowledge of HTML, CSS, JS, PHP and OAuth but no idea. [Graham Grafx](http://grahamgrafx.com/) provided the idea of an app which would change his Twitter profile picture every day of the week and the idea seemed fun enough so I went with it.

tWeekPic was built in less than a week. I just had some issue with the UX  where I wanted the page to be self-contained, and had to use a form that would send a file without reloading the page. To achieve this, I used [valums](https://github.com/valums)'s [file-uploader](https://github.com/valums/file-uploader) javascript library.

Some time later, I forgot to renew the hosting where this app was hosted and decided to end the experiment there.

## How stuff works ##
- Connect with tWeekPic using OAuth.
- Save user OAuth information in a MySQL database.
- Upload a picture for the day you want it to change (IE: sad face on monday, TGIF-face on friday, at the beach on sunday,...).
- Everyday, a cron job runs a script that checks profile pictures that require change.
- If the day requires a change, the new picture is changed using OAuth.

## If you want to run it ##
- Clone the repo
- Grep "mysql_connect" and "mysql_select_db" in the .php files and replace the connection info.
- Grep "consumer" in the .php files and replace the Twitter OAuth info.
- Figure out how the database was built
- Host it and ping me so I can see how you fare.

## Credits / Things I stole ##
- The idea from [Graham Grafx](http://grahamgrafx.com/)
- PHP Oauth Library from [themattharris](https://github.com/themattharris/tmhOAuth)
- Boostrap from [Bootstrap](https://github.com/twitter/bootstrap)
- Modernizr from [Modernizr](https://github.com/Modernizr/Modernizr)
- jQuery from [jQuery](https://github.com/jquery/jquery)
- file-uploader.js from [valums](https://github.com/valums/file-uploader)


