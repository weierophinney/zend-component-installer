<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2015 Zend Technologies Ltd (http://www.zend.com)
 */

namespace Zend\ComponentInstaller\Command;

use Exception;
use Humbug\SelfUpdate\Updater;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Console\ColorInterface as Color;
use ZF\Console\Route;

/**
 * Command for performing a phar self-update.
 */
class SelfUpdate
{
    use ProvideDetailsTrait;

    // @codingStandardsIgnoreStart
    const URL_PHAR = 'https://zendframework.github.io/zend-component-installer/zend-component-installer.phar';
    const URL_VERSION = 'https://zendframework.github.io/zend-component-installer/zend-component-installer.phar.version';
    // @codingStandardsIgnoreEnd

    /**
     * @param Route $route
     * @param Console $console
     * @return int
     */
    public function __invoke(Route $route, Console $console)
    {
        $verbose = $route->getMatchedParam('verbose', false);

        $updater = new Updater();
        $updater->getStrategy()->setPharUrl(self::URL_PHAR);
        $updater->getStrategy()->setVersionUrl(self::URL_VERSION);

        try {
            $result = $updater->update();
            if (! $result) {
                $console->writeLine('No updated needed!', Color::GREEN);
                return 0;
            }
            $new = $updater->getNewVersion();
            $old = $updater->getOldVersion();
            $console->writeLine(sprintf(
                'Updated from %s to %s',
                $old,
                $new
            ), Color::GREEN);

            return 0;
        } catch (Exception $e) {
            $console->writeLine(
                '[ERROR] Could not update',
                Color::RED
            );

            if ($verbose) {
                $this->provideDetails($e, $console);
            }

            return 1;
        }
    }
}
