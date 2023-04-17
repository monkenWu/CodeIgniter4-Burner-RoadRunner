# CodeIgniter4-Burner-RoadRunner

<p align="center">
  <a href="https://ciburner.com//">
    <img src="https://i.imgur.com/YI4RqdP.png" alt="logo" width="200" />
  </a>
</p>

This Library is the RoadRunner Driver for [CodeIgniter4 Burner](https://github.com/monkenWu/CodeIgniter4-Burner).

[English Document](https://ciburner.com/en/roadrunner/)

[正體中文文件](https://ciburner.com/zh_TW/roadrunner/)

## Install

### Prerequisites
1. CodeIgniter Framework 4.2.0^
2. CodeIgniter4-Burner 1.0.0-beta.1
3. Composer
4. PHP8^
5. Enable `php-curl` extension
6. Enable `php-zip` extension
7. Enable `php-sockets` extension

### Composer Install

You can install this Driver with the following command.

```
composer require monken/codeigniter4-burner-roadrunner:1.0.0-beta.1
```

Initialize Server files using built-in commands in the library

```
php spark burner:init RoadRunner
```

Start the server.

```
php spark burner:start
```
