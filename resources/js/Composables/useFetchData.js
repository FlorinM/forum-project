// Fetch data from backend or sessionStorage
export async function useFetchData(route) {
    let retval = [];

    // Make a variable name for cache
    const cacheName = route.replace(/\//g, 'c');

    // Check if data is already stored in sessionStorage
    const storedData = sessionStorage.getItem(cacheName);

    if (storedData) {
        // If data is stored in sessionStorage, use it
        retval = JSON.parse(storedData);
        return retval; // Skip fetching data again
    }

    // If no data in sessionStorage, fetch from the backend
    try {
        const response = await axios.get(route);
        retval = response.data.fetchedData || [];

        // Store the fetched data in sessionStorage for the rest of the session
        sessionStorage.setItem(cacheName, JSON.stringify(retval));
    } catch (error) {
        console.error('Error fetching data on the route ' + route + ': ', error);
    }

    return retval; // Return the fetched data after it has been fully processed
};
