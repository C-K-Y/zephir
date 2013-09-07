<?php

/**
 * UncamelizeOptimizer
 *
 * Optimizes calls to 'uncamelize' using internal function
 */
class UncamelizeOptimizer
{
	/**
	 *
	 */
	public function optimize(array $expression, Call $call, CompilationContext $context)
	{
		if (!isset($expression['parameters'])) {
			return false;
		}

		if (count($expression['parameters']) != 1) {
			return false;
		}

		/**
		 * Process the expected symbol to be returned
		 */
		$call->processExpectedReturn($context);

		$symbolVariable = $call->getSymbolVariable();
		if ($symbolVariable->getType() != 'variable') {
			throw new CompilerException("Returned values by functions can only be assigned to variant variables", $expression);
		}

		if ($call->mustInitSymbolVariable()) {
			$symbolVariable->initVariant($context);
		}

		$resolvedParams = $call->getReadOnlyResolvedParams($expression['parameters'], $context, $expression);
		$context->codePrinter->output('zephir_uncamelize(' . $symbolVariable->getName() . ', ' . $resolvedParams[0] . ');');
		return new CompiledExpression('variable', $symbolVariable->getRealName(), $expression);
	}
}