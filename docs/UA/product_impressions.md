# Product Impressions

## Google Documentation

https://developers.google.com/analytics/devguides/collection/ua/gtm/enhanced-ecommerce#product-impressions

## Introduction

A 'productListType' variable is used to distinguish certain pages on which the products have been shown. From Sylius RC1 it
defaults to the category name with a string prefix ('Category List'). Feel free to set your own naming in `window.productListType`
after the default is set.

## Configuration in GTM

Product impressions are tracked on the default 'pageview'. Make sure to implement
Google Analytics via Google Tag Manager via [the official documentation](https://support.google.com/tagmanager/answer/6107124?hl=en).
