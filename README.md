# Install MeiliServer

## What is MeiliSearch ([GitHub Meili](https://github.com/meilisearch/MeiliSearch/))

⚡ Ultra relevant and instant full-text search API 🔍

MeiliSearch is a powerful, fast, open-source, easy to use, and deploy search engine. The search and indexation are fully customizable and handles features like typo-tolerance, filters, and synonyms.
For more [details about those features, go to our documentation](https://docs.meilisearch.com/).

## About the module

This module need a Meili server to be use.

You problably need to get a root access to your server to install Meili.

You don't need any setup.

## Why use MeiliServer

Because Meili works out-of-the-box, and it's easy to use.

During first test I used it, as promisse, results are blazzing fast.

And the accuracy of the search results is ultra relevant.


## How to install

### Start Mieli server just:
```sh
$ docker run -it --rm -p 7700:7700 -v $(pwd)/data.ms:/data.ms getmeili/meilisearch
```
If you don't know what is the command 'pwd' in $(pwd), please just check on Google.

To check if it's runing well or not, just:
```sh
$ docker container ps
```

### Add a proxy on your nginx config, with theses lines:
```sh
location /instantsearch/ {
   proxy_pass http://127.0.0.1:7700/;
}
```
The proxy path (here 'instantsearch'), need to match with what you set in BO.
To check if it's runing well or not, in your browser, try :
```sh
http(s)://your.shop.com/instantsearch/indexes
```

For the path of the proxy uses in front end to request search result, please don't use 'search', it's already used by Prestashop for the search page.

### Install the module on Prestashop
* Download the archive
* Extract file 
* Repack only files in a new zip archive, and name it 'ao_meili_search.zip' 
* Install the module

That's all, it's ready to work!


##To do
* Be sure there is no way to update meili from front end
* Remove no allow_oosp products
* Manage product visibility
* Add product triger/hook (save, etc..)
* Add category triger/hook (save, etc..)
* Multi shop
* Multi lang
* Add stat on BO
* Add a second way to make to ajax call (proxy php)
* save query searched, and save them in Prestashop DB
* Add a possibility to add some other fields
* Add the Prestashop weight ponderation with ranking parameters

