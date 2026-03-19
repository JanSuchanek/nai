<?php

declare(strict_types=1);

namespace NAi;

use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use NAi\Generator\ClaudeGenerator;
use NAi\Generator\GeminiGenerator;
use NAi\Generator\OpenAiGenerator;

/**
 * Nette DI extension for AI generators.
 *
 * Configuration:
 *   extensions:
 *       ai: NAi\AiExtension
 *
 *   ai:
 *       provider: claude  # claude | openai | gemini
 *       apiKey: %env.AI_API_KEY%
 *       model: null  # optional, auto-detected from provider
 */
class AiExtension extends CompilerExtension
{
	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'provider' => Expect::anyOf('claude', 'openai', 'gemini')->default('claude'),
			'apiKey' => Expect::string()->required(),
			'model' => Expect::string()->nullable()->default(null),
		]);
	}


	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		/** @var \stdClass $config */
		$config = $this->getConfig();

		$generatorClass = match ($config->provider) {
			'openai' => OpenAiGenerator::class,
			'gemini' => GeminiGenerator::class,
			default => ClaudeGenerator::class,
		};

		$args = [$config->apiKey];
		if ($config->model !== null) {
			$args[] = $config->model;
		}

		$builder->addDefinition($this->prefix('generator'))
			->setType(AiGeneratorInterface::class)
			->setFactory($generatorClass, $args);
	}
}
