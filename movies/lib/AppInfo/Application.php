<?php
declare(strict_types=1);
/**
 * @copyright Copyright (c) 2019 John Molakvoæ <skjnldsv@protonmail.com>
 *
 * @author John Molakvoæ <skjnldsv@protonmail.com>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Movies\AppInfo;

use OCP\AppFramework\App;
use OCP\Util;
use \OCA\Movies\Storage\MovieArtStorage;


class Application extends App {

	const APP_ID = 'movies';

	const MIMES = [
        'video/avi',
        'video/flv',
        'video/mkv',
		'video/mp4'
	];

	public function __construct() {
		parent::__construct(self::APP_ID);

		Util::addScript('movies', 'previewplugin');
	}
}
