class SessionStorageService {
  static setItem(key, value) {
    try {
      sessionStorage.setItem(key, JSON.stringify(value));
      return true;
    } catch (error) {
      return false;
    }
  }

  static getItem(key) {
    try {
      const value = sessionStorage.getItem(key);
      return value ? JSON.parse(value) : null;
    } catch (error) {
      return null;
    }
  }

  static removeItem(key) {
    try {
      sessionStorage.removeItem(key);
      return true;
    } catch (error) {
      return false;
    }
  }

  static clear() {
    try {
      sessionStorage.clear();
      return true;
    } catch (error) {
      return false;
    }
  }
}
