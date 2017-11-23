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

declare const OC: OC;

declare function t(app: string, text: string, vars?: any, count?: number, options?: TranslationOptions): string;
declare function n(app: string, textSingular: string, textPlural: string, count: number, vars?: any, options?: TranslationOptions): string;

interface TranslationOptions {
	escape: boolean;
}

interface CurrentUser {
	uid: string;
	displayName: string;
}

interface Share {
	SHARE_TYPE_USER: number,
	SHARE_TYPE_GROUP: number,
	SHARE_TYPE_REMOTE: number,
	SHARE_TYPE_EMAIL: number,
	SHARE_TYPE_CIRCLE: number
}

interface Apps {
	showAppSidebar(): void;
	hideAppSidebar(): void;
}

interface OC {
	requestToken: string;
	Share: Share;
	Apps: Apps;

	generateUrl(url: string, params?: any[], options?: any[]): string;
	linkToOCS(url: string): string;
	getCurrentUser(): CurrentUser;
	getLocale(): string;
}

interface JQuery {
	avatar(user?: string, size?: number, ie8fix?: boolean, hidedefault?: boolean, callback?: () => any, displayname?: string): void;
	imageplaceholder(seed?: string, text?: string, size?: number): void;
	clearimageplaceholder(): void;
}
