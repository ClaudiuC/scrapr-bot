### Scrapr bot
A simple IRC bot that scrapes an SMF forum for updates and spams an IRC channel
with it. Maybe adding some interactive functionality in the future. 

For now it's just a quick, dirty, hard-coded tool. 

### Setup
First, setup a new Heroku app using the
[https://github.com/hhvm/heroku-buildpack-hhvm](HHVM Heroky buildpack). 

Next:

    -- Get the code
    $ git clone https://github.com/ClaudiuC/scrapr-bot
    
    -- Setup the db
    $ heroku addons:add cleardb:ignite 

    -- Actually create the table to store data (wish there was a CLI option)
    -- Open a MySQL client and run the .sql file in the root
    -- To get the connection settings for your client:
    $ heroku config | grep CLEARDB_DATABASE_URL
    CLEARDB_DATABASE_URL =>
    mysql://USER:PASS@HOST/heroku_db?reconnect=true

    -- Add scheduler (this scrapes the forum for new topics)
    $ heroku addons:add scheduler:standard 
    $ heroku addons:open scheduler
    -- In the web interface add:
    $ LD_LIBRARY_PATH=vendor/hhvm/ vendor/hhvm/hhvm -v
    Eval.EnableHipHopSyntax=true crawler/process.php
    
    -- The password for the bot is stored in a config var:
    $ heroku config:set IRC_BOT_PASS=FooBar

    -- Setup the bot worker
    $ heroku config:add LD_LIBRARY_PATH=vendor/hhvm/
    $ heroku ps:scale web=1 worker=1

    -- Set IRC deploy hook (not mandatory)
    $ heroku addons:add deployhooks:irc \
      --server=irc.freenode.net \
      --room=devlounge \
      --nick=mydeploybot \
      --password=secret \
      --message="{{user}} deployed app"

`Skullb0t` is registered and running, so you can instead fork this, register a
new user for your bot and test it on your channel. 
