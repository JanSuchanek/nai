# NAi — Multi-Provider AI Generation for PHP

Lightweight AI content generator with support for multiple providers. Zero framework dependency in core.

## Providers

- **Claude** (Anthropic) — `claude-sonnet-4-20250514`
- **OpenAI** — `gpt-4o`
- **Gemini** (Google) — `gemini-2.0-flash`

## Installation

```bash
composer require jansuchanek/nai
```

## Nette Integration

```neon
extensions:
    ai: NAi\AiExtension

ai:
    provider: claude  # or openai, gemini
    apiKey: %env.ANTHROPIC_API_KEY%
    model: claude-sonnet-4-20250514  # optional
```

## Usage

```php
use NAi\AiGeneratorInterface;

final class MyService
{
    public function __construct(
        private AiGeneratorInterface $ai,
    ) {}

    public function generate(): string
    {
        return $this->ai->generate(
            'Napiš popis produktu',
            'Jsi expert na SEO copywriting.',
        );
    }
}
```

## Requirements

- PHP >= 8.1
- ext-curl
