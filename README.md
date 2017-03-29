# Canteen Stats

Fetches the transaction history of the canteen in B2 and coffee shop on the 1st floor and provides some nice statistics, 
including current balance.


# Installation

`git clone https://github.com/mehdisbys/canteen-stats`

`cd canteen-stats`

`composer install `

#Requirements

You should already have an online account, if not [go create one](http://icashless.systopiacloud.com)

# Usage


Create an .env file mirroring the .env.example file

and then

`php bin/ScrapeScript.php`


# Statistics

The script provides you with the following information :

- Current balance
- Total money topped up
- Total money spent
- Total number of purchases
- Average cost per transaction
- Highest transaction details
- Lowest transaction cost
- Fetches the entire detailled transaction history and stores it locally in JSON format

# Output example 

```json
{
     "current_balance": 5.6,
     "total_paid": 254,
     "latest_transaction_date": "2016-11-21 10:40:00",
     "first_transaction_date": "2016-01-06 11:06:00",
     "total_topped-up": 259.6,
     "number_of_purchases": 105,
     "average_per_transaction": 3.79,
     "highest_paid": {
         "how-much": "5.97",
         "where": "Restaurant Till 2 -  B2",
         "when": "2016-01-15 12:57:00",
         "details" : [...]
         }
}
```

# Privacy policy

- The password is taken from your .env file, it is sent directly to the remote server via HTTPS protocol
- Your transaction history is only stored locally and used as a cache to retrieve only the latest data


# MIT License

MIT - see LICENSE file for more information
