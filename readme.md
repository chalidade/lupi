# LUMEN UNIVERSAL API
LUPI is my first project to simplify and optimize time to create an application project. You can use this to cut off all about backend, just focused on front-end then all about back-end give it to LUPI to solve them.

## Basic Information
- App Name: LUPI (Lumen Universal Application Programming Interface)
- App Domain: [Github LUPI](https://github.com/chalidade/lupi)
- Version: 1.0
- Requires Lumen at least: 5.7.*
- Requires PHP: 7.1.*
- Description: -
- Author: Chalidade
- Author URI: [Chalidade.com](http://chalidade.com)
- License: GNU General Public License v2 or later
- License URI: [![License](https://poser.pugx.org/laravel/lumen-framework/license.svg)](https://packagist.org/packages/laravel/lumen-framework)

## Basic Installation
### Install Composer
Lumen need [composer](https://getcomposer.org/download/) to manage its dependencies. So before using LUPI, make sure you installed on your machine. If you don't know how to install composer just visit my medium post about [Very Simple, How To Install Composer](https://medium.com/@chalidade).

### Download LUPI
After install composer in your machine, download or clone LUPI in this page. You can Download Zip by clicking this [link](https://github.com/chalidade/lupi/archive/master.zip). Then put your file into htdoc if you're using Xampp or var/www/html/ if you're using linux server. then extract.

### Setup composer
Next, open terminal then go to your LUPI Directory. Type and enter
> composer install

This function will automatically install All package or vendor to run LUPI. You will get error like this, if you don't do this step.
> Warning: require_once(D:\xampp\htdocs\lupi\bootstrap/../vendor/autoload.php): failed to open stream: No such file or directory in ...
> Fatal error: require_once(): Failed opening required ‘D:\xampp\htdocs\lupi\bootstrap/../vendor/autoload’ (include_path=’D:\xampp\php\PEAR’) in ...

### Setting Connection
Open folder config/database.php to setting your connection between LUPI and your database. You can copy exampleMysql and change value as your configuration.
> 'exampleMysql'  => [
>    'driver'    => 'mysql',
>    'host'      => env('DB_HOST', 'localhost'),
>    'port'      => env('DB_PORT', 3306),
>    'database'  => env('DB_DATABASE', 'your_database'),
>    'username'  => env('DB_USERNAME', 'your_username'),
>    'password'  => env('DB_PASSWORD', 'your_pass'),
>    'charset'   => env('DB_CHARSET', 'utf8'),
>    'collation' => env('DB_COLLATION', 'utf8_unicode_ci'),
>    'prefix'    => env('DB_PREFIX', ''),
>    'timezone'  => env('DB_TIMEZONE', '+00:00'),
>    'strict'    => env('DB_STRICT_MODE', false),
>],

Make sure your database name, username, and password is right. You can change name of your configuration with rename exampleMysql as you want. Keep it mind, that name will use in parameter LUPI. So make easier.

### Finish
If you open in your browser and get message like this, yes you did it.
> Lumen (5.7.8) (Laravel Components 5.7.)

## Basic Usage
LUPI separate in two global function, Index and Store. That will explain below.

### A.1.Index
Index is cover all requirement to get data from database. It seems like *select* funtion in sql query. You can use join, where, whereIn, whereNotIn, select, orderBy, etc. Complate JSON of Index function is like below.

```
{
	"action"       : "list",
	"db"           : "",
	"table"        : "",
	"where"        : "",
	"whereIn"      : "",
	"whereBetween" : "",
	"whereNotIn"   : "",
	"select"       : "",
	"groupBy"      : "",
	"orderBy"      : "",
	"innerJoin"    : "",
	"leftJoin"     : "",
	"raw"          : {
		"where"    : "",
		"select"   : "",
		"groupBy"  : "",
		"orderBy"  : ""
	},
	"start"        : "",
	"limit"        : ""

}
```

### A.2.Table Of Index Parameter

| No |     Parameter     |                                                     Value                                                    | Required |                                     Function                                     |          Comment          |
|:--:|:-----------------:|:------------------------------------------------------------------------------------------------------------:|:--------:|:--------------------------------------------------------------------------------:|:-------------------------:|
|  1 | "action" :        | "list",                                                                                                      |     Y    | Declare function that will use, this parameter required to running list function | Temporary don't change it |
|  2 | "db" :            | "DbConfigname",                                                                                              |     Y    | Declare name of your config database, change as your configuration name          |                           |
|  3 | "table" :         | "TableName",                                                                                                 |     Y    | Declare table name on database that will execute                                 |                           |
|  4 | "where" :         | [  ["fieldName1","=", "value1"],  ["fieldName2",">", "value2"]  ... ],                                       |     N    | Declare Multiple Where condition                                                 |                           |
|  5 | "whereIn" :       | ["field", ["value1","value2",...]],                                                                          |     N    | Declare WhereIn Condition                                                        |                           |
|  6 | "whereBetween" :  | ["field", ["ValueStart","ValueStop"]],                                                                       |     N    | Declare WhereBetween Condition                                                   |                           |
|  7 | "whereNotIn" :    | ["field", ["value1","value2",...]],                                                                          |     N    | Declare WhereNotIn Condition                                                     |                           |
|  8 | "select" :        | ["field1","field2","field3"...],                                                                             |     N    | Declare selected field that you want to show                                     |                           |
|  9 | "groupBy" :       | "field",                                                                                                     |     N    | Declare groupBy field in your result                                             |                           |
| 10 | "orderBy" :       | ["field","DESC/ASC"],                                                                                        |     N    | Declare orderBy data, choose ASC for ascending or DESC for descending            |                           |
| 11 | "innerJoin" :     | [  ["table2","table2.field", "=", "table1.field"],  ["table3","table3.field", "=", "table1.field"]   .... ], |     N    | Declare multiple inner Join function                                             |                           |
| 12 | "leftJoin" :      | [  ["table2","table2.field", "=", "table1.field"],  ["table3","table3.field", "=", "table1.field"]   .... ], |     N    | Declare multiple left Join Function                                              |                           |
| 13 | "start" :         | "DataStart",                                                                                                 |     N    | Declare get data from                                                            |                           |
| 14 | "limit" :         | "DataLimit"                                                                                                  |     N    | Declare get data limit                                                           |                           |

### A.3.Index Example
#### Get All From Tabel
```
 {
	"action"       : "list",
	"db"           : "sdnpakis",
	"table"        : "tx_hdr_buku_membaca"
 }
```
This json will show us all data from table database sdnpakis on table tx_hdr_buku_membaca.

#### Get Only 5 Data From Tabel
```
 {
	"action"       : "list",
	"db"           : "sdnpakis",
	"table"        : "tx_hdr_buku_membaca",
  "limit"        : "5"
 }
```
This json will show us only 5 data from table database sdnpakis on table tx_hdr_buku_membaca.

#### Get Data From Tabel with Where Parameter
```
 {
	"action"       : "list",
	"db"           : "sdnpakis",
	"table"        : "tx_hdr_buku_membaca",
  "where"        : [["membaca_id","=","1"]]
 }
```
This json will show us only data with membaca_id = 1 from table database sdnpakis on table tx_hdr_buku_membaca.


#### Get Data From Tabel with Join and Where Parameter
```
 {
	"action"       : "list",
	"db"           : "sdnpakis",
	"table"        : "tx_hdr_buku_membaca a",
  "join"         : [["tx_hdr_buku b","b.buku_id","=","membaca_buku_id"]],
  "where"        : [["membaca_status","=","1"]]
 }
```
This json will show us only data with membaca_status = 1 from table database sdnpakis on table tx_hdr_buku_membaca inner join with tx_hdr_buku with buku_id = membaca_buku_id.
