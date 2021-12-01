describe 'colorpicker', ->
    Helper = require './SpecHelper'
    colorpicker = null
    input = null

    beforeEach ->
      colorpicker = Helper.colorpicker
      input = Helper.input

    it 'should be possible to open the colorpicker', ->
        expect(colorpicker.isVisible()).toEqual false
        colorpicker.open()
        expect(colorpicker.isVisible()).toEqual true

    it 'should correctly set the color output after a paste', ->
        colorpicker.open()
        colorpicker.chooseColorAtIndex(4)
        expect(input.getValue()).toBe '#ffff00'

        blue = '#0000ff'
        colorpicker.enterValue blue
        expect(input.getValue()).toBe blue

    it 'should be possible to enable/disable the color picker', ->
        expect(colorpicker.isVisible()).toEqual false
        colorpicker.disable()
        colorpicker.open()
        expect(colorpicker.isVisible()).toEqual false

    it 'should be able to cancel the selection', ->
        fuchsia = '#ff00ff'
        input.setValue fuchsia

        colorpicker.open()
        colorpicker.chooseColorAtIndex(4)
        colorpicker.cancel()

        expect(input.getValue()).toBe fuchsia
