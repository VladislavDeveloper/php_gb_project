<?php

namespace Blog\Commands;
use Blog\Exceptions\ArgumentsException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\Type\VoidType;

class ArgumentsTest extends TestCase
{

    // Провайдер данных
    public function argumentsProvider(): iterable
    {
        return [
            ['some_string', 'some_string'],
            // Тестовый набор
            // Первое значение будет передано
            // в тест первым аргументом,
            // второе значение будет передано
            // в тест вторым аргументом
            [' some_string', 'some_string'],
            // Тестовый набор №2
            [' some_string ', 'some_string'],
            [123, '123'],
            [12.3, '12.3'],
        ];
    }

    /**
    * @dataProvider argumentsProvider
    */
    public function testItConvertsArgumentsToString(
        $inputValue,
        $expectedValue
    ): void
    {
        $arguments = new Arguments(['some_key' => $inputValue]);
        $value = $arguments->get('some_key');
        $this->assertEquals($expectedValue, $value);
    }

    public function testItReturnsArgumentsValueByName(): Void
    {
        //AAA - Arrange, Act, Assert
        //Подготовка, действие, проверка

        //Подготовка
        $arguments = new Arguments(['some_key' => 'some_value']);

        //Действие
        $value = $arguments->get('some_key');

        //Проверка
        $this->assertEquals('some_value', $value);
    }

    public function testItReturnsValueAsString(): void
    {
        $arguments = new Arguments(['some_key' => 1234]);

        $value = $arguments->get('some_key');

        $this->assertSame('1234', $value);
    }

    public function testItThrowsAnExceptionWhenArgumentIsAbsent(): void
    {
        //Тестирование исключений

        //Подготовка объекта для тестирования
        $arguments = new Arguments([]);

        //Описание типа ожидаемого исключения

        $this->expectException(ArgumentsException::class);

        //Сообщение, выбрасываемое исключением
        $this->expectExceptionMessage("No such argument: some_key");

        //Выполняем действие
        $arguments->get('some_key');
    }
}
