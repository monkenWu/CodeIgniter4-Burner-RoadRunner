# CodeIgniter4-Burner-RoadRunner

This Library is the RoadRunner Driver for [CodeIgniter4 Burner](https://github.com/monkenWu/CodeIgniter4-Burner).

## Install

### Prerequisites
1. CodeIgniter Framework 4.2.0^
2. CodeIgniter4-Burner 0.4.0^
3. Composer
4. PHP8^
5. Enable `php-curl` extension
6. Enable `php-zip` extension
7. Enable `php-sockets` extension

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

### start server

When you do not pass any parameters, it will be preset to start the server.

```
php spark burner:start
```

By default, burner reads the default driver written in `app/Burner.php`. Of course, you can force Burner to execute commands with the `RoadRunner` driver by using a parameter like this：

```
php spark burner:start --driver RoadRunner
```

> `--driver RoadRunner` This parameter also applies to all the commands mentioned below.

You can also use the following parameters to construct your commands according to your needs.

* -c: path to the config file.
* -w: set the working directory.
* --dotenv: populate the process with env variables from the .dotenv file.
* -d: start a pprof server. Note, this is not debug, to use debug logs level, please, use logs: https://roadrunner.dev/docs/plugins-logger/2.x/en
* -s: silent mode.
* -o: to override configuration keys with your values, e.g.
  ```
  -o=http.address=:8080
  ```
  will override the http.address from the .rr.yaml.

> Note that Burner already uses the `-c`, `-p` and `-w` parameters and you need to avoid using the same parameters again.

#### daemon mode

Let RoadRunner work in the background.

When you run the server with this option, Burner will ignore the Automatic reload setting.

```
php spark burner:start --daemon
```

Using this mode Burner will direct the output to `/dev/null` and you must define your `log_output` in `.rr.yaml` to look like this:

```yaml
logs:
  mode: development
  output: stdout
  file_logger_options:
    log_output: "{{log_path}}"
    max_size: 100
    max_age: 1
    max_backups : 5
    compress: false
```

### stop server

This command runs in daemon mode only.

```
php spark burner:stop
```

Force the server to close.

```
php spark burner:stop -f
```

### workers status

Get the current running information of all Workers.

```
php spark burner:rr workers
```

Continuously updated interaction mode every second.

```
php spark burner:rr workers -i
```

### more command

Run commands directly to RoadRunner's rr binary.

```
php spark burner:rr [rr_comands]
```

You can refer to the official [RoadRunner documentation](https://roadrunner.dev/docs/app-server-cli/2.x/en) to construct your commands. 

> Note that Burner already uses the `-c`, `-p` and `-w` parameters and you need to avoid using the same parameters again.

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

Since the RoadRunner has fundamentally difference with other server software(i.e. Nginx, Apache), every Codeigniter4 will persist inside RAMs as the form of Worker, HTTP requests will reuse these Workers to process. Hence, we have better develop and test stability under the circumstance with only one Worker to prove it can also work properly under serveral Workers in the formal environment.

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
