
// src/utils/auth.js
export function isAdmin() {
  return localStorage.getItem('admin') === 'true';
}
