# Slack + Jenkis + Wit.Ai = Natural language deploy
## About
This project is just a proof-of-concept made in a couple of hours so use it on your own risk as it has no guarantee that will work for you.  

This project is able to :

Receive an Slack app_mention message (https://api.slack.com/events/app_mention) -> send the message to a wit.ai application to recognize some build parameters that are really specific for my application > Send a request to Jenkins to start the build with the detected parameters
