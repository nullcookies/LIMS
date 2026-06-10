import { Box, Typography, Table, TableBody, TableCell, TableContainer, TableHead, TableRow, Paper } from '@mui/material';
import { useQuery } from '@tanstack/react-query';
import { endpoints } from '../api/endpoints';

export default function TestsPage() {
  const { data } = useQuery({
    queryKey: ['testMethods'],
    queryFn: () => endpoints.testMethods.list(),
  });
  const methods = data?.data?.['hydra:member'] || [];

  return (
    <Box>
      <Typography variant="h4" gutterBottom>Test Methods</Typography>
      <TableContainer component={Paper}>
        <Table>
          <TableHead>
            <TableRow>
              <TableCell>Code</TableCell>
              <TableCell>Name</TableCell>
              <TableCell>Unit</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {methods.map((m: { id: number; code: string; name: string; unit?: string }) => (
              <TableRow key={m.id}>
                <TableCell>{m.code}</TableCell>
                <TableCell>{m.name}</TableCell>
                <TableCell>{m.unit || '-'}</TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </TableContainer>
    </Box>
  );
}
