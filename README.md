# Vendor Data Exporter, _by [Private Packagist](https://packagist.com)_

A command-line tool to fetch, from the Private Packagist API, information about a vendor's customers, the packages added to them, and the package versions they have access to according to the limitations set.

## Installation

```shell
composer install --no-dev
```

### Executable Binary

This project has a `box.json` configuration file for building a PHAR executable via [Box](https://github.com/box-project/box) (Box itself is not included in the dev dependencies, so follow [installation instructions](https://github.com/box-project/box/blob/master/doc/installation.md) to install globally). To compile a PHAR executable of Vendor Data Exporter, run the following command:

```shell
box compile
```

The resulting PHAR executable can be used in the following examples by replacing `bin/console` with `bin/packagist-vendor-data-exporter.phar`.

## Usage

### Authentication

To use Vendor Data Exporter, you must authenticate with the Private Packagist API using an API Token and Secret. These can be provided either via command flags or environment variables.

API credentials can be found on [Private Packagist](https://packagist.com) at: _Your Organization &rarr; Settings &rarr; API Access_.

#### Command Flags

The API token and secret can be passed in via the command flags `--token` and `--secret` respectively.

```shell
bin/console \
    --token="<REPLACE-WITH-YOUR-TOKEN>" \
    --secret="<REPLACE-WITH-YOUR-SECRET>"
```

API credentials passed in via the command flags take precedence over environment variables.

#### Environment Variables

The API token and secret can also be set in the environment variables `PACKAGIST_API_TOKEN` and `PACKAGIST_API_SECRET` respectively.

```shell
export PACKAGIST_API_TOKEN="<REPLACE-WITH-YOUR-TOKEN>"
export PACKAGIST_API_SECRET="<REPLACE-WITH-YOUR-SECRET>"
bin/console
```

Vendor Data Exporter will also check for the existence of a `.env` environment file in the current working directory, too. Real environment variables take precedence over environment variables defined in `.env`.

```shell
cat ".env"
PACKAGIST_API_TOKEN=<REPLACE-WITH-YOUR-TOKEN>
PACKAGIST_API_SECRET=<REPLACE-WITH-YOUR-SECRET>

bin/console
```
### Output Format

#### Text

By default, the application will output the list of customer packages and versions in a text-based table to be viewed in the terminal (the `txt` format). Use this with a command such as `less` or `more` to view the entire list.

```shell
bin/console | less
```

#### JSON

Another output format is JSON (`json`).

```shell
# Example: use the JQ command to further manipulate the data.
bin/console --format="json" | jq --raw-output ".[].name" | sort | xargs -l -i echo 'Customer "{}" found.'
```

#### CSV

The final output is comma-separated values (`csv`).

```shell
# Example: upload the resulting CSV database to a webhook endpoint (for example, for your accounting software).
bin/console --format="csv" | curl -fsSL --request 'POST' --data '@-' "https://accounting.depts.intranet/uploads/monthly-customer-data" --header "Authorization: Bearer ${ACCOUNTING_CREDENTIALS}"
```
