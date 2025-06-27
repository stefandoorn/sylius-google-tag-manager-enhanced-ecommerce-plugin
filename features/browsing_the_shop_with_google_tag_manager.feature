@browsing_the_shop_with_google_tag_manager
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
    And the store allows paying Offline
    And I am a logged in customer

  @ui @javascript
  Scenario: Triggering view_cart event when viewing the cart
    Given I have product "Super Cool T-Shirt" in the cart
    And I see the summary of my cart
    Then the "view_cart" Google Tag Manager event should be triggered
    And this event should contain proper "view_cart" GTM data

  @ui @javascript
  Scenario: Triggering add_to_cart event when adding product to cart
    Given I have product "Super Cool T-Shirt" in the cart
    Then I should be on my cart summary page
    And the "add_to_cart" Google Tag Manager event should be triggered
    And this event should contain proper "add_to_cart" GTM data

  @ui @javascript
  Scenario: Triggering remove_from_cart event when removing product from cart
    Given I have product "Super Cool T-Shirt" in the cart
    And I remove product "Super Cool T-Shirt" from the cart
    When I am on the summary of my cart page
    Then the "remove_from_cart" Google Tag Manager event should be triggered
    And this event should contain proper "remove_from_cart" GTM data

  @ui @javascript
  Scenario: Triggering checkout step events when completing checkout steps
    Given I have product "Super Cool T-Shirt" in the cart
    And I addressed the cart
    And the "begin_checkout" Google Tag Manager event should be triggered
    And this event should contain proper "begin_checkout" GTM data
    When I select "Free" shipping method
    And I complete the shipping step
    And the "add_shipping_info" Google Tag Manager event should be triggered
    And this event should contain proper "add_shipping_info" GTM data
    When I select "Offline" payment method
    And I complete the payment step
    And the "add_payment_info" Google Tag Manager event should be triggered
    And this event should contain proper "add_payment_info" GTM data
    When I confirm my order
    Then the "purchase" Google Tag Manager event should be triggered
    And this event should contain proper "purchase" GTM data
