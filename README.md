# CodeIgniter4-Burner-RoadRunner

This Library is the RoadRunner Driver for [CodeIgniter4 Burner](https://github.com/monkenWu/CodeIgniter4-Burner).

## Install

### Prerequisites
1. CodeIgniter Framework 4.2.0^
2. Composer
3. PHP8^
4. Enable `php-curl` extension
5. Enable `php-zip` extension
6. Enable `php-sockets` extension

### Composer Install

You can install this Driver with the following command.

```
composer require monken/codeigniter4-burner-roadrunner
```

Initialize Server files using built-in commands in the library

```
php spark burner:init RoadRunner
```

## Command

When you do not pass any parameters, it will be preset to start the server.

```
php spark burner:start RoadRunner
```

###  Stopping RR

* Send a SIGINT or SIGTERM syscalls to the main RoadRunner process. Inside a k8s for example, this is done automatically when you're stopping the pod.
* If you want to stop RR manually, you may hit a ctrl+c for the graceful stop or hit ctrl+c one more time to force stop.

You may also use a commands:

```
php spark burner:start RoadRunner serve -p
```

### stop server

Where -p means: create a .pid file. And then:

```
php spark burner:start RoadRunner stop
```

Or to force stop:

```
php spark burner:start RoadRunner stop -f
```

### more command

```
php spark burner:start RoadRunner [serve, reset, workers, stop] [-Parameters]
```

* serve:
    * --dotenv: populate the process with env variables from the .dotenv file.
    * -d: start a pprof server. Note, this is not debug, to use debug logs level, please, use logs: https://roadrunner.dev/docs/plugins-logger/2.x/en
    * -s: silent mode.
    * -o: to override configuration keys with your values, e.g. -o=http.address=:8080 will override the http.address from the .rr.yaml.
    * -p: create a .pid file to use ./rr stop later.
* reset
* workers:
    * -i: interactive mode (update statistic every second).
* stop:
    * -f: force stop.
    * -s: silent mode.

## RoadRunner Server Settings

The server settings are all in the project root directory ".rr.yaml". The default file will look like this:

```yaml
version: "2.7"

rpc:
  listen: tcp://127.0.0.1:6001

server:
  command: "php Worker.php"
  # env:
  #   XDEBUG_SESSION: 1

http:
  address: "0.0.0.0:8080"
  static:
    dir: "/app/dev/public"
    forbid: [".htaccess", ".php"]
  pool:
    num_workers: 1
    # max_jobs: 64
    # debug: true

# reload:
#   interval: 1s
#   patterns: [ ".php" ]
#   services:
#     http:
#       recursive: true
#       ignore: [ "vendor" ]
#       patterns: [ ".php", ".go", ".dmd" ]
#       dirs: [ "/app/dev" ]
```

You can create your configuration file according to the [Roadrunner document](https://roadrunner.dev/docs/intro-config).

## Development Suggestions

### Automatic reload

In the default circumstance of RoadRunner, you must restart the server everytime after you revised any PHP files so that your revision will effective.
It seems not that friendly during development.

#### RoadRunner

You can revise your `.rr.yaml` configuration file, add the settings below and start the development mode with `-d`.
RoadRunner Server will detect if the PHP files were revised or not, automatically, and reload the Worker instantly.

```yaml
reload:
  interval: 1s
  patterns: [ ".php" ]
  services:
    http:
      recursive: true
      ignore: [ "vendor" ]
      patterns: [ ".php", ".go", ".md" ]
      dirs: [ "." ]
```
### Developing and debugging in a environment with only one Worker

Since the RoadRunner and Workerman has fundamentally difference with other server software(i.e. Nginx, Apache), every Codeigniter4 will persist inside RAMs as the form of Worker, HTTP requests will reuse these Workers to process. Hence, we have better develop and test stability under the circumstance with only one Worker to prove it can also work properly under serveral Workers in the formal environment.

#### RoadRunner

You can reference the `.rr.yaml` settings below to lower the amount of Worker to the minimum:

```yaml
http:
  address: "0.0.0.0:8080"
  static:
    dir: "./public"
    forbid: [".htaccess", ".php"]
  pool:
    num_workers: 1
    # max_jobs: 64
    # debug: true
```
