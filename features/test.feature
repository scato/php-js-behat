Feature: include JS code in tests
  In order to have fast but complete acceptance tests
  As a developer
  I want to run front-end JS in my tests

  Scenario: perform a fake API call
    Given a fake API client
    When I call the client
    And the server responds
    Then I should have a fake reply

  Scenario: perform a bad API call
    Given a fake API client
    When I call the client with the wrong request
    And the server responds
    Then I should have a fake error message
