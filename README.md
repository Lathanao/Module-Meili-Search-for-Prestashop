Install MeiliServer
================================================================================

What is MeiliSearch ([GitHub Meili](https://github.com/meilisearch/MeiliSearch/))
--------
⚡ Ultra relevant and instant full-text search API 🔍

MeiliSearch is a powerful, fast, open-source, easy to use, and deploy search engine. The search and indexation are fully customizable and handles features like typo-tolerance, filters, and synonyms.
For more [details about those features, go to our documentation](https://docs.meilisearch.com/).

About the module
--------
This module need a Meili server to be use.

You problably need to get a root access to your server to install Meili.

You don't need any setup.

Why not use ElasticSearch
--------
Because Meili works out-of-the-box, and it's easier to use than ElasticSearch.

During first test I used it, as promisse, results are blazzing fast.

And the accuracy of the search results is ultra relevant.


How to install
--------
To start Mieli server just run:
```sh
$ docker run -it --rm -p 7700:7700 -v $(pwd)/data.ms:/data.ms getmeili/meilisearch
```
If you don't know what is the command 'pwd' in $(pwd), please just check on Google.

Then, just check it's runing well with:
```sh
$ docker container ps
```

Then install the module on Prestashop.

That's all, it's probably ready to works!

