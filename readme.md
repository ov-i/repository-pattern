[![CI](https://github.com/ov-i/repository-pattern/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/ov-i/repository-pattern/actions/workflows/ci.yml)

# Repository pattern

I've created a simple library for my future projects to make my life easier.
This architecture has been tested on real medium-sized logistics/LMS codebases.

No guarantees — use at your own risk.

If you find this library useful, feel free to use it.
Feedback is welcome.

## Installation

Since this project is not published on any package manager yet, you can install it 
by cloning this repository and keeping it somewhere accessible via Composer's path repository.

1. Clone this repository somewhere on your disk.
2. In your project `composer.json` add the following:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "/absolute/or/relative/path/to/repository-pattern",
            "options": { "symlink": true }
        }
    ]
}
```

3. Require this package:
```bash
composer require ov-i/repository-pattern
```

4. Have a wonderful day!

## Development
1. Run checks:
```bash
composer ci
```

2. Install git hooks (local repo config):
```bash
composer hooks:install
```

