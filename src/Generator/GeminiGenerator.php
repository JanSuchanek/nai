<?php

declare(strict_types=1);

namespace NAi\Generator;

use NAi\AiGeneratorInterface;

/**
 * Google Gemini 2.0 Flash content generator.
 */
final class GeminiGenerator implements AiGeneratorInterface
{
	private const API_URL = 'https://generativelanguage.googleapis.com/v1beta/models/{model}:generateContent';


	public function __construct(
		private readonly string $apiKey,
		private readonly string $model = 'gemini-2.0-flash',
	) {}


	public function generate(string $prompt, string $context = ''): string
	{
		$fullPrompt = $context !== '' ? $context . "\n\n" . $prompt : $prompt;

		$url = str_replace('{model}', $this->model, self::API_URL) . '?key=' . $this->apiKey;

		$ch = curl_init($url);
		curl_setopt_array($ch, [
			CURLOPT_POST => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 60,
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
			],
			CURLOPT_POSTFIELDS => json_encode([
				'contents' => [
					['parts' => [['text' => $fullPrompt]]],
				],
				'generationConfig' => [
					'maxOutputTokens' => 2048,
					'temperature' => 0.7,
				],
			]) ?: '',
		]);

		$response = (string) curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($code !== 200) {
			throw new \RuntimeException('Gemini API error (HTTP ' . $code . '): ' . $response);
		}

		/** @var array{candidates: list<array{content: array{parts: list<array{text: string}>}}>} $data */
		$data = json_decode($response, true);
		return trim((string) ($data['candidates'][0]['content']['parts'][0]['text'] ?? ''));
	}


	public function getName(): string
	{
		return 'Gemini 2.0 Flash';
	}
}
