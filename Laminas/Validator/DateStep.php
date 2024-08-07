<?php

declare(strict_types=1);

namespace Laminas\Validator;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Laminas\Stdlib\ArrayUtils;
use Traversable;

use function array_combine;
use function array_count_values;
use function array_map;
use function array_shift;
use function ceil;
use function date_default_timezone_get;
use function explode;
use function floor;
use function func_get_args;
use function in_array;
use function is_array;
use function max;
use function min;
use function preg_match;
use function sprintf;
use function str_starts_with;

use const PHP_INT_MAX;

/** @final */
class DateStep extends Date
{
    /**
     * Validity constants
     */
    public const NOT_STEP = 'dateStepNotStep';

    /**
     * Default format constant
     */
    public const FORMAT_DEFAULT = DateTime::ISO8601;

    /**
     * Validation failure message template definitions
     *
     * @var string[]
     */
    protected $messageTemplates = [
        self::INVALID      => 'Invalid type given. String, integer, array or DateTime expected',
        self::INVALID_DATE => 'The input does not appear to be a valid date',
        self::FALSEFORMAT  => "The input does not fit the date format '%format%'",
        self::NOT_STEP     => 'The input is not a valid step',
    ];

    /**
     * Optional base date value
     *
     * @var string|int|DateTimeInterface
     */
    protected $baseValue = '1970-01-01T00:00:00Z';

    /**
     * Date step interval (defaults to 1 day).
     * Uses the DateInterval specification.
     *
     * @var DateInterval
     */
    protected $step;

    /**
     * Optional timezone to be used when the baseValue
     * and validation values do not contain timezone info
     *
     * @deprecated Since 2.61.0 - The timezone option is unused
     *
     * @var DateTimeZone
     */
    protected $timezone;

    /**
     * Set default options for this instance
     *
     * @param string|array|Traversable $options
     */
    public function __construct($options = [])
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (! is_array($options)) {
            $options           = func_get_args();
            $temp              = [];
            $temp['baseValue'] = array_shift($options);
            if (! empty($options)) {
                $temp['step'] = array_shift($options);
            }
            if (! empty($options)) {
                $temp['format'] = array_shift($options);
            }
            if (! empty($options)) {
                $temp['timezone'] = array_shift($options);
            }

            $options = $temp;
        }

        if (! isset($options['step'])) {
            $options['step'] = new DateInterval('P1D');
        }
        if (! isset($options['timezone'])) {
            $options['timezone'] = new DateTimeZone(date_default_timezone_get());
        }

        parent::__construct($options);
    }

    /**
     * Sets the base value from which the step should be computed
     *
     * @deprecated Since 2.61.0 - All option setters and getters will be removed in 3.0
     *
     * @param string|int|DateTimeInterface $baseValue
     * @return $this
     */
    public function setBaseValue($baseValue)
    {
        $this->baseValue = $baseValue;
        return $this;
    }

    /**
     * Returns the base value from which the step should be computed
     *
     * @deprecated Since 2.61.0 - All option setters and getters will be removed in 3.0
     *
     * @return string|int|DateTimeInterface
     */
    public function getBaseValue()
    {
        return $this->baseValue;
    }

    /**
     * Sets the step date interval
     *
     * @deprecated Since 2.61.0 - All option setters and getters will be removed in 3.0
     *
     * @return $this
     */
    public function setStep(DateInterval $step)
    {
        $this->step = $step;
        return $this;
    }

    /**
     * Returns the step date interval
     *
     * @deprecated Since 2.61.0 - All option setters and getters will be removed in 3.0
     *
     * @return DateInterval
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * Returns the timezone option
     *
     * @deprecated Since 2.61.0 - All option setters and getters will be removed in 3.0
     *
     * @return DateTimeZone
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Sets the timezone option
     *
     * @deprecated Since 2.61.0 - All option setters and getters will be removed in 3.0
     *
     * @return $this
     */
    public function setTimezone(DateTimeZone $timezone)
    {
        $this->timezone = $timezone;
        return $this;
    }

    /**
     * Supports formats with ISO week (W) definitions
     *
     * @see Date::convertString()
     *
     * @param string $value
     * @param bool $addErrors
     * @return DateTime|false
     */
    protected function convertString($value, $addErrors = true)
    {
        // Custom week format support
        if (
            str_starts_with($this->format, 'Y-\WW')
            && preg_match('/^([0-9]{4})\-W([0-9]{2})/', $value, $matches)
        ) {
            $date = new DateTime();
            $date->setISODate((int) $matches[1], (int) $matches[2]);
        } else {
            $date = DateTime::createFromFormat($this->format, $value, new DateTimeZone('UTC'));
        }

        // Invalid dates can show up as warnings (ie. "2007-02-99")
        // and still return a DateTime object.
        $errors = DateTime::getLastErrors();
        if (is_array($errors) && $errors['warning_count'] > 0) {
            if ($addErrors) {
                $this->error(self::FALSEFORMAT);
            }
            return false;
        }

        return $date;
    }

    /**
     * Returns true if a date is within a valid step
     *
     * @param string|int|DateTimeInterface $value
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function isValid($value)
    {
        if (! parent::isValid($value)) {
            return false;
        }

        $valueDate = $this->convertToDateTime($value, false); // avoid duplicate errors
        $baseDate  = $this->convertToDateTime($this->baseValue, false);

        if (false === $valueDate || false === $baseDate) {
            return false;
        }

        $step = $this->getStep();

        // Same date?
        // phpcs:ignore SlevomatCodingStandard.Operators.DisallowEqualOperators.DisallowedEqualOperator
        if ($valueDate == $baseDate) {
            return true;
        }

        // Optimization for simple intervals.
        // Handle intervals of just one date or time unit.
        $intervalParts = explode('|', $step->format('%y|%m|%d|%h|%i|%s'));
        $intervalParts = array_map('intval', $intervalParts);
        $partCounts    = array_count_values($intervalParts);

        $unitKeys      = ['years', 'months', 'days', 'hours', 'minutes', 'seconds'];
        $intervalParts = array_combine($unitKeys, $intervalParts);

        // Get absolute time difference to avoid special cases of missing/added time
        $absoluteValueDate = new DateTime($valueDate->format('Y-m-d H:i:s'), new DateTimeZone('UTC'));
        $absoluteBaseDate  = new DateTime($baseDate->format('Y-m-d H:i:s'), new DateTimeZone('UTC'));

        $timeDiff  = $absoluteValueDate->diff($absoluteBaseDate, true);
        $diffParts = array_map('intval', explode('|', $timeDiff->format('%y|%m|%d|%h|%i|%s')));
        $diffParts = array_combine($unitKeys, $diffParts);

        if (5 === $partCounts[0]) {
            // Find the unit with the non-zero interval
            $intervalUnit = 'days';
            $stepValue    = 1;
            foreach ($intervalParts as $key => $value) {
                if (0 !== $value) {
                    $intervalUnit = $key;
                    $stepValue    = $value;
                    break;
                }
            }

            // Check date units
            if (in_array($intervalUnit, ['years', 'months', 'days'])) {
                switch ($intervalUnit) {
                    case 'years':
                        if (
                            0 === $diffParts['months'] && 0 === $diffParts['days']
                            && 0 === $diffParts['hours'] && 0 === $diffParts['minutes']
                            && 0 === $diffParts['seconds']
                        ) {
                            if (($diffParts['years'] % $stepValue) === 0) {
                                return true;
                            }
                        }
                        break;
                    case 'months':
                        if (
                            0 === $diffParts['days'] && 0 === $diffParts['hours']
                            && 0 === $diffParts['minutes'] && 0 === $diffParts['seconds']
                        ) {
                            $months = ($diffParts['years'] * 12) + $diffParts['months'];
                            if (($months % $stepValue) === 0) {
                                return true;
                            }
                        }
                        break;
                    case 'days':
                        if (
                            0 === $diffParts['hours'] && 0 === $diffParts['minutes']
                            && 0 === $diffParts['seconds']
                        ) {
                            $days = (int) $timeDiff->format('%a'); // Total days
                            if (($days % $stepValue) === 0) {
                                return true;
                            }
                        }
                        break;
                }
                $this->error(self::NOT_STEP);
                return false;
            }

            // Check time units
            if (in_array($intervalUnit, ['hours', 'minutes', 'seconds'])) {
                // Simple test if $stepValue is 1.
                if (1 === $stepValue) {
                    if (
                        'hours' === $intervalUnit
                        && 0 === $diffParts['minutes'] && 0 === $diffParts['seconds']
                    ) {
                        return true;
                    } elseif ('minutes' === $intervalUnit && 0 === $diffParts['seconds']) {
                        return true;
                    } elseif ('seconds' === $intervalUnit) {
                        return true;
                    }

                    $this->error(self::NOT_STEP);

                    return false;
                }

                // Simple test for same day, when using default baseDate
                if (
                    $baseDate->format('Y-m-d') === $valueDate->format('Y-m-d')
                    && $baseDate->format('Y-m-d') === '1970-01-01'
                ) {
                    switch ($intervalUnit) {
                        case 'hours':
                            if (0 === $diffParts['minutes'] && 0 === $diffParts['seconds']) {
                                if (($diffParts['hours'] % $stepValue) === 0) {
                                    return true;
                                }
                            }
                            break;
                        case 'minutes':
                            if (0 === $diffParts['seconds']) {
                                $minutes = ($diffParts['hours'] * 60) + $diffParts['minutes'];
                                if (($minutes % $stepValue) === 0) {
                                    return true;
                                }
                            }
                            break;
                        case 'seconds':
                            $seconds = ($diffParts['hours'] * 60 * 60)
                                       + ($diffParts['minutes'] * 60)
                                       + $diffParts['seconds'];
                            if (($seconds % $stepValue) === 0) {
                                return true;
                            }
                            break;
                    }
                    $this->error(self::NOT_STEP);
                    return false;
                }
            }
        }

        return $this->fallbackIncrementalIterationLogic($baseDate, $valueDate, $intervalParts, $diffParts, $step);
    }

    /**
     * Fall back to slower (but accurate) method for complex intervals.
     * Keep adding steps to the base date until a match is found
     * or until the value is exceeded.
     *
     * This is really slow if the interval is small, especially if the
     * default base date of 1/1/1970 is used. We can skip a chunk of
     * iterations by starting at the lower bound of steps needed to reach
     * the target
     *
     * @param int[] $intervalParts
     * @param int[] $diffParts
     * @throws Exception\InvalidArgumentException
     */
    private function fallbackIncrementalIterationLogic(
        DateTimeInterface $baseDate,
        DateTimeInterface $valueDate,
        array $intervalParts,
        array $diffParts,
        DateInterval $step
    ): bool {
        [$minSteps, $requiredIterations] = $this->computeMinStepAndRequiredIterations($intervalParts, $diffParts);
        $minimumInterval                 = $this->computeMinimumInterval($intervalParts, $minSteps);
        $isIncrementalStepping           = $baseDate < $valueDate;

        if (! ($baseDate instanceof DateTime || $baseDate instanceof DateTimeImmutable)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Function %s requires the baseDate to be a DateTime or DateTimeImmutable instance.',
                __FUNCTION__
            ));
        }

        for ($offsetIterations = 0; $offsetIterations < $requiredIterations; $offsetIterations += 1) {
            if ($isIncrementalStepping) {
                $baseDate = $baseDate->add($minimumInterval);
            } else {
                $baseDate = $baseDate->sub($minimumInterval);
            }
        }

        while (
            ($isIncrementalStepping && $baseDate < $valueDate)
            || (! $isIncrementalStepping && $baseDate > $valueDate)
        ) {
            if ($isIncrementalStepping) {
                $baseDate = $baseDate->add($step);
            } else {
                $baseDate = $baseDate->sub($step);
            }

            // phpcs:ignore SlevomatCodingStandard.Operators.DisallowEqualOperators.DisallowedEqualOperator
            if ($baseDate == $valueDate) {
                return true;
            }
        }

        $this->error(self::NOT_STEP);

        return false;
    }

    /**
     * Computes minimum interval to use for iterations while checking steps
     *
     * @param int[] $intervalParts
     * @param int|float $minSteps
     */
    private function computeMinimumInterval(array $intervalParts, $minSteps): DateInterval
    {
        return new DateInterval(sprintf(
            'P%dY%dM%dDT%dH%dM%dS',
            $intervalParts['years'] * $minSteps,
            $intervalParts['months'] * $minSteps,
            $intervalParts['days'] * $minSteps,
            $intervalParts['hours'] * $minSteps,
            $intervalParts['minutes'] * $minSteps,
            $intervalParts['seconds'] * $minSteps
        ));
    }

    /**
     * @param int[] $intervalParts
     * @param int[] $diffParts
     * @return int[] (ordered tuple containing minimum steps and required step iterations
     * @psalm-return array{0: int, 1: int}
     */
    private function computeMinStepAndRequiredIterations(array $intervalParts, array $diffParts): array
    {
        $minSteps = $this->computeMinSteps($intervalParts, $diffParts);

        // If we use PHP_INT_MAX DateInterval::__construct falls over with a bad format error
        // before we reach the max on 64 bit machines
        $maxInteger = min(2 ** 31, PHP_INT_MAX);
        // check for integer overflow and split $minimum interval if needed
        $maximumInterval        = max($intervalParts);
        $requiredStepIterations = 1;

        if (($minSteps * $maximumInterval) > $maxInteger) {
            $requiredStepIterations = ceil(($minSteps * $maximumInterval) / $maxInteger);
            $minSteps               = floor($minSteps / $requiredStepIterations);
        }

        return [(int) $minSteps, $minSteps !== 0 ? (int) $requiredStepIterations : 0];
    }

    /**
     * Multiply the step interval by the lower bound of steps to reach the target
     *
     * @param int[] $intervalParts
     * @param int[] $diffParts
     * @return float|int
     */
    private function computeMinSteps(array $intervalParts, array $diffParts)
    {
        $intervalMaxSeconds = $this->computeIntervalMaxSeconds($intervalParts);

        return 0 === $intervalMaxSeconds
            ? 0
            : max(floor($this->computeDiffMinSeconds($diffParts) / $intervalMaxSeconds) - 1, 0);
    }

    /**
     * Get upper bound of the given interval in seconds
     * Converts a given `$intervalParts` array into seconds
     *
     * @param int[] $intervalParts
     */
    private function computeIntervalMaxSeconds(array $intervalParts): int
    {
        return ($intervalParts['years'] * 60 * 60 * 24 * 366)
            + ($intervalParts['months'] * 60 * 60 * 24 * 31)
            + ($intervalParts['days'] * 60 * 60 * 24)
            + ($intervalParts['hours'] * 60 * 60)
            + ($intervalParts['minutes'] * 60)
            + $intervalParts['seconds'];
    }

    /**
     * Get lower bound of difference in secondss
     * Converts a given `$diffParts` array into seconds
     *
     * @param int[] $diffParts
     */
    private function computeDiffMinSeconds(array $diffParts): int
    {
        return ($diffParts['years'] * 60 * 60 * 24 * 365)
            + ($diffParts['months'] * 60 * 60 * 24 * 28)
            + ($diffParts['days'] * 60 * 60 * 24)
            + ($diffParts['hours'] * 60 * 60)
            + ($diffParts['minutes'] * 60)
            + $diffParts['seconds'];
    }
}
