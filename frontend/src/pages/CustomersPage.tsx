import { Box, Typography, List, ListItem, ListItemText, Paper } from '@mui/material';
import { useQuery } from '@tanstack/react-query';
import { endpoints } from '../api/endpoints';

export default function CustomersPage() {
  const { data } = useQuery({
    queryKey: ['customers'],
    queryFn: () => endpoints.customers.list(),
  });
  const customers = data?.data?.['hydra:member'] || [];

  return (
    <Box>
      <Typography variant="h4" gutterBottom>Customers</Typography>
      <Paper>
        <List>
          {customers.map((c: { id: number; name: string; email?: string; phone?: string }) => (
            <ListItem key={c.id}>
              <ListItemText primary={c.name} secondary={`${c.email || ''} ${c.phone || ''}`} />
            </ListItem>
          ))}
        </List>
      </Paper>
    </Box>
  );
}
