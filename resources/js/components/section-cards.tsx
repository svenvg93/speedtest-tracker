import { IconTrendingDown, IconTrendingUp, IconArrowDown, IconArrowUp, IconClock } from "@tabler/icons-react";
import { Badge } from "@/components/ui/badge";
import {
  Card,
  CardAction,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";

export type StatCard = {
  label: string;
  value: string | number;
  description: string;
  badge: {
    icon: React.ReactNode;
    text: string;
    up?: boolean;
  };
  footer: {
    text: string;
    icon: React.ReactNode;
    colorClass?: string;
  }[];
};

export function SectionCards({ stats }: { stats: StatCard[] }) {
  return (
    <div className="grid grid-cols-1 gap-4 px-4 lg:grid-cols-3">
      {stats.map((stat, i) => (
        <Card key={i}>
        <CardHeader>
            <CardDescription>{stat.label}</CardDescription>
          <CardTitle className="text-2xl font-semibold tabular-nums @[250px]/card:text-3xl">
              {stat.value}
          </CardTitle>
          <CardAction>
            <Badge variant="outline">
                {stat.badge.icon}
                {stat.badge.text}
            </Badge>
          </CardAction>
        </CardHeader>
        <CardFooter className="flex-col items-start gap-1.5 text-sm">
            {stat.footer.map((f, j) => (
              <div className={`line-clamp-1 flex gap-2 font-medium ${f.colorClass ?? ""}`} key={j}>
                {f.text} {f.icon}
              </div>
            ))}
        </CardFooter>
      </Card>
      ))}
    </div>
  );
} 