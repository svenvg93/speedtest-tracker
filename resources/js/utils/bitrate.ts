/**
 * Convert bytes to bits (1 byte = 8 bits)
 */
export function bytesToBits(bytes: number | null): number | null {
  if (bytes === null || bytes === undefined) {
    return null;
  }
  
  if (bytes < 0) {
    throw new Error('Bytes value cannot be negative');
  }
  
  // 1 byte = 8 bits
  return Math.round(bytes * 8);
}

/**
 * Convert bits to Mbps
 */
export function bitsToMbps(bits: number | null): number | null {
  if (bits === null || bits === undefined) {
    return null;
  }
  
  // Convert bits to Mbps (1 Mbps = 1,000,000 bits)
  return bits / 1_000_000;
}

/**
 * Convert bytes to Mbps for chart display
 */
export function bytesToMbps(bytes: number | null): number | null {
  if (bytes === null || bytes === undefined) {
    return null;
  }
  
  const bits = bytesToBits(bytes);
  return bitsToMbps(bits);
}

/**
 * Format bits to human readable string
 */
export function formatBits(bits: number | null, precision: number = 2): string {
  if (bits === null || bits === undefined) {
    return 'N/A';
  }
  
  if (bits === 0) {
    return '0 bps';
  }
  
  const units = ['bps', 'Kbps', 'Mbps', 'Gbps', 'Tbps'];
  const divisor = 1000;
  const power = Math.floor(Math.log(bits) / Math.log(divisor));
  const unitIndex = Math.min(power, units.length - 1);
  
  return `${(bits / Math.pow(divisor, unitIndex)).toFixed(precision)} ${units[unitIndex]}`;
} 