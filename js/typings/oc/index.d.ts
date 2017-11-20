/**
 * @copyright Copyright (c) 2017 Kai Schröer <git@schroeer.co>
 *
 * @author Kai Schröer <git@schroeer.co>
 *
 * @license GNU AGPL version 3 or any later version
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

declare function t(app: string, text: string): string;

declare namespace OC {
	let requestToken: string;

	function generateUrl(url: string, params?: any[], options?: any[]): string;
	function getCurrentUser(): CurrentUser;
	function getLocale(): string;

	interface CurrentUser {
		uid: string;
		displayName: string;
	}

	class Apps {
		showAppSidebar(): void;
		hideAppSidebar(): void;
	}
}

declare namespace OCP {
	class AppConfig {
		getKeys(app: string, options: object): void;
		getValue(app: string, key: string, defaultValue: string, options: object): void;
		setValue(app: string, key: string, value: string, options: object): void;
		deleteKey(app: string, key: string, options: object): void;
	}
}
