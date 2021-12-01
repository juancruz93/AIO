/* global browser, global, protractor, By */
var port = process.env.E2E_SANDBOX_PORT || 8765;
var sandboxUrl = 'http://localhost:' + port + '/';

global.By = protractor.By;

beforeEach(function() {
  browser.get(sandboxUrl);
});

var colorpicker = {
  isVisible: function() {
    return element(By.css('.sp-container')).isDisplayed();
  },

  cancel: function() {
    element(by.css('.sp-cancel')).click();
  },

  disable: function() {
    element(By.id('disabled')).click();
  },

  open: function() {
    element(By.css('.sp-replacer')).click();
  },

  chooseColorAtIndex: function(i) {
    element.all(By.css('.sp-thumb-el')).get(i).click();
  },

  enterValue: function(value) {
    element(By.css('.sp-input'))
      .clear()
      .sendKeys(value)
      .sendKeys(protractor.Key.ENTER);
  }
};

var input = {
  get: function() {
    return element(By.id('color'));
  },
  getValue: function() {
    return input.get().getAttribute('value');
  },
  setValue: function(value) {
    input.get().sendKeys(value);
  }
};

module.exports = {
  input: input,
  colorpicker: colorpicker
};
