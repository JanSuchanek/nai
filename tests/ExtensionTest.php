<?php

declare(strict_types=1);

namespace NAi\Tests;

use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use NAi\AiExtension;
use NAi\AiGeneratorInterface;
use NAi\Generator\ClaudeGenerator;
use NAi\Generator\GeminiGenerator;
use NAi\Generator\OpenAiGenerator;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../vendor/autoload.php';

\Tester\Environment::setup();

/**
 * Tests for AiExtension — DI container configuration.
 */
class ExtensionTest extends TestCase
{
	private function createContainer(array $config): Container
	{
		$loader = new ContainerLoader(sys_get_temp_dir() . '/nette-ai-test', true);
		$class = $loader->load(function (Compiler $compiler) use ($config): void {
			$compiler->addExtension('ai', new AiExtension());
			$compiler->addConfig(['ai' => $config]);
		}, md5(json_encode($config)));

		return new $class();
	}


	public function testClaudeProvider(): void
	{
		$container = $this->createContainer([
			'provider' => 'claude',
			'apiKey' => 'test-key',
		]);

		$gen = $container->getByType(AiGeneratorInterface::class);
		Assert::type(ClaudeGenerator::class, $gen);
	}


	public function testOpenAiProvider(): void
	{
		$container = $this->createContainer([
			'provider' => 'openai',
			'apiKey' => 'test-key',
		]);

		$gen = $container->getByType(AiGeneratorInterface::class);
		Assert::type(OpenAiGenerator::class, $gen);
	}


	public function testGeminiProvider(): void
	{
		$container = $this->createContainer([
			'provider' => 'gemini',
			'apiKey' => 'test-key',
		]);

		$gen = $container->getByType(AiGeneratorInterface::class);
		Assert::type(GeminiGenerator::class, $gen);
	}


	public function testDefaultProviderIsClaude(): void
	{
		$container = $this->createContainer([
			'apiKey' => 'test-key',
		]);

		$gen = $container->getByType(AiGeneratorInterface::class);
		Assert::type(ClaudeGenerator::class, $gen);
	}
}

(new ExtensionTest())->run();
