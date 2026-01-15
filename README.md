# air_ds_website

Website for booking airplane tickets.

## How to use

Install with the following command:

```bash
git clone https://github.com/dimitriszitonoulis/air_ds_website.git
```

You will need to install [xampp](https://www.apachefriends.org/) to run an `apache` web server and a `SQL` database

After installing it run it as administrator and click on `start` on
`Apache` and `MySQL`.

You will need to place this repository's contents under:
`<path_to_xampp_folder>\htdocs`.

After that open a browser and visit: http://localhost/air_ds_website

This URL uses the `index.php` file to initialize the database and redirect to
http://localhost/air_ds_website/client/pages/home.php.
In this step a lot of entries are added in the database
(flights, users, reservations for each flight), so give it some time.

Everytime this URL is visited the database will drop all entries and
initialize using the
[initialize file](./server/database/db_utils/db_initialize.php).

After that you can book a flight, register login etc.

### Selecting seats

When selecting a seat click on the passenger info box and then on the seat
you want to select for that passenger.
If you want change an already selected seat, click on the passenger info,
then on the previously selected seat for that passenger
(the name of the seat is in the passenger info) and then select the new seat.
