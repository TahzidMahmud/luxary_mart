import { t } from 'i18next';

function getStrToLangKey(inputString: string) {
    // Convert to lowercase, replace spaces with underscores
    let modifiedString = inputString.toLowerCase().replace(/ /g, '_');

    // Remove non-alphanumeric characters and underscores
    let sanitizedString = modifiedString.replace(/[^a-z0-9_]/g, '');

    return sanitizedString;
}

/**
 * takes a string, converts it to the translation key and returns the translation
 * it can also take the translation key directly
 * @param key string
 * @returns
 */
export const translate = (key?: string) => {
    if (!key) return '';

    return t(getStrToLangKey(key));
};
