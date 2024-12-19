export async function useFetchData(route, cacheDuration = 600000) {
        // Default cache duration is 10 minutes (600000 ms)

    let retval = [];

    // Make a variable name for cache
    const cacheName = route.replace(/\//g, 'c');

    // A separate cache for the timestamp
    const cacheTimestampName = cacheName + "_timestamp";

    // Check if data is already stored in sessionStorage
    const storedData = sessionStorage.getItem(cacheName);
    const cacheTimestamp = sessionStorage.getItem(cacheTimestampName);

    if (storedData && cacheTimestamp) {
        const currentTime = new Date().getTime();
        const timeElapsed = currentTime - parseInt(cacheTimestamp);

        // Check if the data is still valid based on the cache duration (in ms)
        if (timeElapsed < cacheDuration) {
            // If data is still within the cache duration, use it
            retval = JSON.parse(storedData);
            return retval; // Skip fetching data again
        } else {
            // If cache has expired, clear the stored data
            sessionStorage.removeItem(cacheName);
            sessionStorage.removeItem(cacheTimestampName);
        }
    }

    // If no valid data in sessionStorage, fetch from the backend
    try {
        const response = await axios.get(route);
        retval = response.data.fetchedData || [];

        // Store the fetched data and current timestamp in sessionStorage
        sessionStorage.setItem(cacheName, JSON.stringify(retval));
        sessionStorage.setItem(cacheTimestampName, new Date().getTime().toString());
    } catch (error) {
        console.error('Error fetching data on the route ' + route + ': ', error);
    }

    return retval; // Return the fetched data after it has been fully processed
};
