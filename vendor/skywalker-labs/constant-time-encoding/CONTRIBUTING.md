# Contributing to Skywalker Constant-Time Encoding

Thank you for considering contributing to the Skywalker Constant-Time Encoding library!

## Getting Started

1. **Fork** the repository on GitHub.
2. **Clone** your fork locally:

   ```sh
   git clone https://github.com/skywalker-labs/constant-time-encoding.git
   cd constant-time-encoding
   ```

3. **Install dependencies**:

   ```sh
   composer install
   ```

## Development

### Running Tests

We use PHPUnit for testing. Ensure all tests pass before submitting a Pull Request.

```sh
vendor/bin/phpunit
```

### Static Analysis

We might use Psalm for static analysis. You can check your code with:

```sh
vendor/bin/psalm
```

(Note: Ensure `psalm.xml` is configured correctly if you run this).

## Submission Guidelines

1. Create a new branch for your feature or fix.
2. Write clear, concise commit messages.
3. Ensure your code follows PSR-12 coding standards.
4. Submit a Pull Request to the `main` branch.

## License

By contributing, you agree that your contributions will be licensed under the MIT License of this project.
