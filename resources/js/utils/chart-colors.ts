// Shared color palette for server consistency across charts
export const serverColors = [
  "#22c55e", // green
  "#3b82f6", // blue
  "#f59e0b", // amber
  "#ef4444", // red
  "#8b5cf6", // violet
  "#06b6d4", // cyan
  "#84cc16", // lime
  "#f97316", // orange
  "#ec4899", // pink
  "#10b981", // emerald
  "#6366f1", // indigo
  "#fbbf24", // yellow
];

// Get consistent color for a server name
export function getServerColor(serverName: string): string {
  // Simple hash function to get consistent index
  let hash = 0;
  for (let i = 0; i < serverName.length; i++) {
    const char = serverName.charCodeAt(i);
    hash = ((hash << 5) - hash) + char;
    hash = hash & hash; // Convert to 32-bit integer
  }
  
  // Use absolute value and modulo to get index
  const index = Math.abs(hash) % serverColors.length;
  return serverColors[index];
}

// Get colors for multiple servers in order
export function getServerColors(serverNames: string[]): string[] {
  return serverNames.map(getServerColor);
} 