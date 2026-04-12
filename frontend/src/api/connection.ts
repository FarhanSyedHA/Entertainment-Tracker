const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

export function callAPI(endpoint: string, method: 'GET' | 'POST' | 'PUT' | 'DELETE',body?: object) {
  const token = localStorage.getItem('token')
  return fetch(
                API_URL + endpoint,
                {
                  method,
                  headers: { 
                              'Content-Type': 'application/json',
                              ...(token && {Authorization: `Bearer ${token}`}), // spread nothing if token is null
                           },
                  body: body ? JSON.stringify(body) : null,  
                }
              )
              .then((response) => response.json())
              .then((data) => {
                if (data.error) throw new Error(data.error)
                return data
              })
}