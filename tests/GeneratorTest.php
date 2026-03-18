<?php

declare(strict_types=1);

namespace NAi\Tests;

use NAi\AiGeneratorInterface;
use NAi\Generator\ClaudeGenerator;
use NAi\Generator\GeminiGenerator;
use NAi\Generator\OpenAiGenerator;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../vendor/autoload.php';

\Tester\Environment::setup();

/**
 * Tests for AI generators — interface compliance and error handling.
 * API calls are not made (no real keys), so we test instantiation,
 * interface compliance, and error handling.
 */
class GeneratorTest extends TestCase
{
	public function testClaudeImplementsInterface(): void
	{
		$gen = new ClaudeGenerator('test-key');
		Assert::type(AiGeneratorInterface::class, $gen);
		Assert::same('Claude 3.5 Sonnet', $gen->getName());
	}


	public function testOpenAiImplementsInterface(): void
	{
		$gen = new OpenAiGenerator('test-key');
		Assert::type(AiGeneratorInterface::class, $gen);
		Assert::same('OpenAI GPT-4o-mini', $gen->getName());
	}


	public function testGeminiImplementsInterface(): void
	{
		$gen = new GeminiGenerator('test-key');
		Assert::type(AiGeneratorInterface::class, $gen);
		Assert::same('Gemini 2.0 Flash', $gen->getName());
	}


	public function testCustomModel(): void
	{
		$gen = new ClaudeGenerator('key', 'claude-3-opus-20240229');
		Assert::type(AiGeneratorInterface::class, $gen);

		$gen2 = new OpenAiGenerator('key', 'gpt-4');
		Assert::type(AiGeneratorInterface::class, $gen2);

		$gen3 = new GeminiGenerator('key', 'gemini-pro');
		Assert::type(AiGeneratorInterface::class, $gen3);
	}


	public function testClaudeThrowsOnBadKey(): void
	{
		$gen = new ClaudeGenerator('invalid-key');
		Assert::exception(
			fn() => $gen->generate('Hello'),
			\RuntimeException::class,
			'~Claude API error~',
		);
	}


	public function testOpenAiThrowsOnBadKey(): void
	{
		$gen = new OpenAiGenerator('invalid-key');
		Assert::exception(
			fn() => $gen->generate('Hello'),
			\RuntimeException::class,
			'~OpenAI API error~',
		);
	}


	public function testGeminiThrowsOnBadKey(): void
	{
		$gen = new GeminiGenerator('invalid-key');
		Assert::exception(
			fn() => $gen->generate('Hello'),
			\RuntimeException::class,
			'~Gemini API error~',
		);
	}
}

(new GeneratorTest())->run();
