/**
 * Converts PHP date format to JavaScript toLocaleDateString options
 * @param phpFormat - PHP date format string (e.g., 'M. j - G:i')
 * @returns JavaScript toLocaleDateString options object
 */
export function phpFormatToJsOptions(phpFormat: string): Intl.DateTimeFormatOptions {
  const options: Intl.DateTimeFormatOptions = {};
  
  // Convert PHP format to JavaScript options
  if (phpFormat.includes('M')) {
    options.month = 'short'; // M = short month name
  }
  if (phpFormat.includes('F')) {
    options.month = 'long'; // F = full month name
  }
  if (phpFormat.includes('m')) {
    options.month = '2-digit'; // m = month with leading zeros
  }
  if (phpFormat.includes('n')) {
    options.month = 'numeric'; // n = month without leading zeros
  }
  
  if (phpFormat.includes('j')) {
    options.day = 'numeric'; // j = day without leading zeros
  }
  if (phpFormat.includes('d')) {
    options.day = '2-digit'; // d = day with leading zeros
  }
  
  if (phpFormat.includes('G')) {
    options.hour = 'numeric'; // G = 24-hour format without leading zeros
    options.hour12 = false; // Explicitly set to 24-hour format
  }
  if (phpFormat.includes('H')) {
    options.hour = '2-digit'; // H = 24-hour format with leading zeros
    options.hour12 = false; // Explicitly set to 24-hour format
  }
  if (phpFormat.includes('g')) {
    options.hour = 'numeric'; // g = 12-hour format without leading zeros
    options.hour12 = true; // Explicitly set to 12-hour format
  }
  if (phpFormat.includes('h')) {
    options.hour = '2-digit'; // h = 12-hour format with leading zeros
    options.hour12 = true; // Explicitly set to 12-hour format
  }
  
  if (phpFormat.includes('i')) {
    options.minute = '2-digit'; // i = minutes with leading zeros
  }
  
  // AM/PM is now handled by the hour format (g, h for 12-hour, G, H for 24-hour)
  // A and a are still valid but redundant when using g/h/G/H
  
  if (phpFormat.includes('Y')) {
    options.year = 'numeric'; // Y = full year
  }
  if (phpFormat.includes('y')) {
    options.year = '2-digit'; // y = 2-digit year
  }
  
  return options;
}

/**
 * Formats a date using PHP format string converted to JavaScript options
 * @param date - Date to format
 * @param phpFormat - PHP date format string
 * @returns Formatted date string
 */
export function formatDateWithPhpFormat(date: Date, phpFormat: string): string {
  const options = phpFormatToJsOptions(phpFormat);
  return date.toLocaleDateString('en-US', options);
} 