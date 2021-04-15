<?php
/*
 * @copyright 2021 Christoph Wurst <christoph@winzerhof-wurst.at>
 *
 * @author 2021 Christoph Wurst <christoph@winzerhof-wurst.at>
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
 */

declare(strict_types=1);

/*
 * @copyright 2021 Christoph Wurst <christoph@winzerhof-wurst.at>
 *
 * @author 2021 Christoph Wurst <christoph@winzerhof-wurst.at>
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
 */

namespace OCA\DAV\CalDAV\Trashbin;

use OCA\DAV\CalDAV\CalDavBackend;
use OCA\DAV\CalDAV\Calendar;
use Sabre\DAV\Exception\Forbidden;
use Sabre\DAV\Exception\NotFound;
use Sabre\DAV\ICollection;
use Sabre\DAV\IMoveTarget;
use Sabre\DAV\INode;

class RestoreTarget implements ICollection, IMoveTarget {
	public const NAME = 'restore';

	/** @var CalDavBackend */
	private $caldavBackend;

	public function __construct(CalDavBackend $caldavBackend) {
		$this->caldavBackend = $caldavBackend;
	}

	public function createFile($name, $data = null) {
		throw new Forbidden();
	}

	public function createDirectory($name) {
		throw new Forbidden();
	}

	public function getChild($name) {
		throw new NotFound();
	}

	public function getChildren(): array {
		return [];
	}

	public function childExists($name): bool {
		return false;
	}

	public function moveInto($targetName, $sourcePath, INode $sourceNode): bool {
		if ($sourceNode instanceof Calendar) {
			$this->caldavBackend->restoreCalendar((int) $sourceNode->getResourceId());
			return true;
		}
		if ($sourceNode instanceof DeletedCalendarObject) {
			$this->caldavBackend->restoreCalendarObject($sourceNode->getId());
			return true;
		}

		return false;
	}

	public function delete() {
		throw new Forbidden();
	}

	public function getName(): string {
		return 'restore';
	}

	public function setName($name) {
		throw new Forbidden();
	}

	public function getLastModified() {
		return 0;
	}
}
