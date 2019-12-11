* * * * * sleep 10; curl --silent https://www.rigoride.com/api/cronCheckRideStatus |tee -a /var/www/html/rigoride/cronlogs/cronCheckRideStatus.log
* * * * * sleep 10; curl --silent https://www.rigoride.com/api/cronScheduledRequest |tee -a /var/www/html/rigoride/cronlogs/cronScheduledRequest.log
