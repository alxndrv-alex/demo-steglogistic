class ValidationService {
  static validatePhoneNumber = phoneNumber => {
    const regex = /^\d{6,16}$/;
    return regex.test(phoneNumber);
  };

  static validateEmail = email => {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
  };

  static validateFullName = name => {
    const regex = /^[\p{L}\p{M}.\-]+(?: [\p{L}\p{M}.\-]+)*$/u;
    return regex.test(name.trim());
  };

  static validateNumber = (number, min, max) => {
    if (typeof number === 'string') {
      return Number(number) >= min && Number(number) <= max;
    }

    return number >= min && number <= max;
  };

  static validateAddress = value => {
    const regex = /^[\p{L}\p{M}\d\s,.\-\/]{1,200}$/u;
    if (typeof value === 'string') {
      return regex.test(value.trim());
    }
  };

  static validatePostcode = postcode => {
    const regex = /^\d{5}$/;
    return regex.test(postcode);
  };

  static validateStringLength = (value, min, max) => {
    return value.length >= min && value.length <= max;
  };
}
