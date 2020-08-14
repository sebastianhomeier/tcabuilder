<?php
namespace SpoonerWeb\TcaBuilder\Unit\Tests;

/*
 * This file is part of a TYPO3 extension.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use SpoonerWeb\TcaBuilder\TcaBuilder;

class TcaBuilderTest extends \Nimut\TestingFramework\TestCase\AbstractTestCase
{
    /**
     * @var \SpoonerWeb\TcaBuilder\TcaBuilder
     */
    protected $tcaBuilder;

    public function setUp()
    {
        $this->tcaBuilder = new TcaBuilder();
        $this->tcaBuilder->loadConfiguration('table', 'type');
    }

    public function tearDown()
    {
        unset($this->tcaBuilder);
    }

    /**
     * @test
     */
    public function classCanBeInstantiated()
    {
        self::assertInstanceOf(TcaBuilder::class, $this->tcaBuilder);
    }

    /**
     * @test
     */
    public function AddNoFieldAndSaveDirectlyReturnsEmptyString()
    {
        $this->tcaBuilder
            ->saveToTca();

        self::assertEquals('', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function addFieldWithStringAddsField()
    {
        $this->tcaBuilder
            ->addField('newField')
            ->saveToTca();

        self::assertEquals('newField', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function addFieldWithStringAndAlternativeLabelAddsFieldWithLabel()
    {
        $this->tcaBuilder
            ->addField('newField', '', 'Label')
            ->saveToTca();

        self::assertEquals('newField;Label', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function addTwoFieldsWithStringsAddsTwoFields()
    {
        $this->tcaBuilder
            ->addField('newField')
            ->addField('newSecondField')
            ->saveToTca();


        self::assertEquals('newField,newSecondField', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function addTwoFieldsWithStringsAndOneWithPositionAddsTwoCorrectlySortedFields()
    {
        $this->tcaBuilder
            ->addField('newField')
            ->addField('newSecondField', 'before:newField')
            ->saveToTca();

        self::assertEquals('newSecondField,newField', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function addTwoFieldsWithStringsAndOneWithNonExistingPositionAddsTwoCorrectlySortedFields()
    {
        $this->tcaBuilder
            ->addField('newField')
            ->addField('newSecondField', 'before:nonExistingField')
            ->saveToTca();

        self::assertEquals('newField,newSecondField', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function addThreeFieldsWithStringsAndTwoWithPositionAddsThreeCorrectlySortedFields()
    {
        $this->tcaBuilder
            ->addField('newField')
            ->addField('newSecondField', 'before:newField')
            ->addField('newThirdField', 'before:newField')
            ->saveToTca();

        self::assertEquals('newSecondField,newThirdField,newField', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function removeFieldWithStringRemovesField()
    {
        $this->tcaBuilder
            ->addField('newField')
            ->removeField('newField')
            ->saveToTca();

        self::assertEquals('', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function removeFieldWithNonExistingStringRemovesNoField()
    {
        $this->tcaBuilder
            ->addField('newField')
            ->removeField('nonExistingField')
            ->saveToTca();

        self::assertEquals('newField', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function moveFieldWithStringAndPositionReturnsFieldListInCorrectOrder()
    {
        $this->tcaBuilder
            ->addField('newField')
            ->addField('newSecondField')
            ->addField('newThirdField')
            ->moveField('newThirdField', 'after:newField')
            ->saveToTca();

        self::assertEquals('newField,newThirdField,newSecondField', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function moveFieldWithStringAndPositionAndLabelReturnsFieldListInCorrectOrder()
    {
        $this->tcaBuilder
            ->addField('newField')
            ->addField('newSecondField')
            ->addField('newThirdField')
            ->moveField('newThirdField', 'before:newSecondField', 'newLabel')
            ->saveToTca();

        self::assertEquals('newField,newThirdField;newLabel,newSecondField', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function moveFieldWithStringAndNonExistingPositionReturnsFieldListInOriginalOrder()
    {
        $this->tcaBuilder
            ->addField('newField')
            ->addField('newSecondField')
            ->addField('newThirdField')
            ->moveField('newThirdField', 'before:nonExistingField', 'newLabel')
            ->saveToTca();

        self::assertEquals('newField,newSecondField,newThirdField', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function addPaletteWithStringReturnsPaletteString()
    {
        $this->tcaBuilder
            ->addPalette('newPalette')
            ->saveToTca();

        self::assertEquals('--palette--;;newPalette', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function addPaletteWithStringAndAlternativeLabelReturnsPaletteStringWithLabel()
    {
        $this->tcaBuilder
            ->addPalette('newPalette', '', 'newLabel')
            ->saveToTca();

        self::assertEquals('--palette--;newLabel;newPalette', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function addTwoPalettesWithStringsReturnsPaletteString()
    {
        $this->tcaBuilder
            ->addPalette('newPalette')
            ->addPalette('newSecondPalette')
            ->saveToTca();

        self::assertEquals('--palette--;;newPalette,--palette--;;newSecondPalette', $GLOBALS['TCA']['table']['types']['type']['showitem']);
    }

    /**
     * @test
     */
    public function addThreePalettesWithStringsAndOneWithExactPositionStringReturnsPaletteString()
    {
        $this->tcaBuilder
            ->addPalette('newPalette')
            ->addPalette('newSecondPalette')
            ->addPalette('newThirdPalette', 'before:--palette--;;newSecondPalette')
            ->saveToTca();

        self::assertEquals(
            '--palette--;;newPalette,--palette--;;newThirdPalette,--palette--;;newSecondPalette',
            $GLOBALS['TCA']['table']['types']['type']['showitem']
        );
    }

    /**
     * @test
     */
    public function addThreePalettesWithStringsAndOneWithPositionStringUsingFunctionReturnsPaletteString()
    {
        $this->tcaBuilder
            ->addPalette('newPalette')
            ->addPalette('newSecondPalette')
            ->addPalette('newThirdPalette', 'before:' . $this->tcaBuilder->getPaletteString('newSecondPalette'))
            ->saveToTca();

        self::assertEquals(
            '--palette--;;newPalette,--palette--;;newThirdPalette,--palette--;;newSecondPalette',
            $GLOBALS['TCA']['table']['types']['type']['showitem']
        );
    }

    /**
     * @test
     */
    public function addThreePalettesWithStringsAndOneWithLabelAndOneWithPositionStringUsingFunctionReturnsPaletteString()
    {
        $this->tcaBuilder
            ->addPalette('newPalette')
            ->addPalette('newSecondPalette', '', 'newLabel')
            ->addPalette('newThirdPalette', 'before:' . $this->tcaBuilder->getPaletteString('newSecondPalette'))
            ->saveToTca();

        self::assertEquals(
            '--palette--;;newPalette,--palette--;;newThirdPalette,--palette--;newLabel;newSecondPalette',
            $GLOBALS['TCA']['table']['types']['type']['showitem']
        );
    }

}
