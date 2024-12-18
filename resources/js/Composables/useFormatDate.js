
/**
 * Utility function to format date.
 * @param {string} dateString
 * @returns {string}
 */
export function useFormatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' });
}
