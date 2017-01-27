[![Build Status](https://travis-ci.org/transifex/transifex-live-wordpress.svg?branch=devel)](https://travis-ci.org/transifex/transifex-live-wordpress)

# International SEO by Transifex

This branch creates a dockerized development environment in order to test the transifex-live-wordpress plugin

## Instructions

* ```git checkout docker```
* ```mv docker ../transifex-wordpress-docker```
* ```git checkout devel```
* ```cd ../transifex-wordpress-docker```
* ```docker-compose up```

If everything goes well, you will have a wordpress instance listening to port localhost:8080 with the transifex plugin activated from the code at ../transifex-live-wordpress (username: root/ password: root)
