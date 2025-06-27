@browsing_the_shop_inventory_with_google_tag_manager
Feature: Google Tag Manager Enhanced Ecommerce events
  In order to track customer behavior
  As a Store Owner
  I want to have Enhanced Ecommerce events triggered throughout the shopping experience

  Background:
    Given the store operates on a single channel in "United States"
    And the store classifies its products as "T-Shirts"
    And the store has a product "Super Cool T-Shirt" priced at "$20.00" belonging to the "T-Shirts" taxon
    And the store has a product "PHP T-Shirt" priced at "$10.00" belonging to the "T-Shirts" taxon
    And the store ships everywhere for Free
    And I am a logged in customer

  @ui @javascript
  Scenario: Triggering view_item_list event when browsing product catalog
    When I browse products from taxon "T-Shirts"
    Then the "view_item_list" Google Tag Manager event should be triggered
    And this event should contain proper "view_item_list" GTM data

  @ui @javascript
  Scenario: Triggering view_item event when viewing product details
    When I view product "Super Cool T-Shirt"
    Then the "view_item" Google Tag Manager event should be triggered
    And this event should contain proper "view_item" GTM data
