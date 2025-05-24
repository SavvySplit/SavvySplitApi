import React from 'react';
import {
  Box,
  Typography,
  TextField,
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableRow,
  Paper,
} from '@mui/material';

interface DynamicJsonRendererProps {
  data: Record<string, any>;
}

const formatKey = (key: string): string =>
  key.replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase());

const isPlainObject = (val: any) =>
  typeof val === 'object' && val !== null && !Array.isArray(val);

const renderArrayTable = (arr: any[]) => {
  if (!arr.length || !arr.some(isPlainObject)) return null;

  const headers = Array.from(
    arr.reduce((set, obj) => {
      if (isPlainObject(obj)) {
        Object.keys(obj).forEach((key) => set.add(key));
      }
      return set;
    }, new Set<string>())
  );

  return (
    <Table size="small" sx={{ mt: 1 }}>
      <TableHead>
        <TableRow>
          {headers.map((key, index) => (
            <TableCell key={index}>{formatKey(key)}</TableCell>
          ))}
        </TableRow>
      </TableHead>
      <TableBody>
        {arr.map((item, rowIndex) => (
          <TableRow key={rowIndex}>
            {headers.map((key, colIndex) => (
              <TableCell key={colIndex}>
                {isPlainObject(item[key]) || Array.isArray(item[key])
                  ? renderNested(item[key], 0)
                  : item[key] ?? ''}
              </TableCell>
            ))}
          </TableRow>
        ))}
      </TableBody>
    </Table>
  );
};

const renderNested = (value: any, depth: number): React.ReactNode => {
  if (Array.isArray(value)) {
    if (value.length > 0 && isPlainObject(value[0])) {
      return renderArrayTable(value);
    }

    return (
      <Box sx={{ ml: 2 }}>
        {value.map((item, idx) => (
          <TextField
            key={idx}
            variant="outlined"
            value={
              typeof item === 'object'
                ? JSON.stringify(item, null, 2)
                : item ?? ''
            }
            fullWidth
            multiline
            minRows={1}
            size="small"
            InputProps={{ readOnly: true }}
            sx={{ backgroundColor: '#f3f4f6', mb: 1 }}
          />
        ))}
      </Box>
    );
  }

  if (isPlainObject(value)) {
    return (
      <Box sx={{ ml: depth > 0 ? 2 : 0 }}>
        {Object.entries(value).map(([k, v]) => (
          <Box
            key={k}
            sx={{
              display: 'flex',
              alignItems: 'center',
              mb: 1,
              ml: depth > 0 ? 2 : 0,
            }}
          >
            <Typography sx={{ width: 200, fontWeight: 500 }}>
              {formatKey(k)}
            </Typography>
            <Box sx={{ ml: 2, width: '100%' }}>
              {isPlainObject(v) || Array.isArray(v) ? (
                renderNested(v, depth + 1)
              ) : (
                <TextField
                  variant="outlined"
                  value={
                    typeof v === 'object'
                      ? JSON.stringify(v, null, 2)
                      : v ?? ''
                  }
                  fullWidth
                  multiline
                  minRows={1}
                  size="small"
                  InputProps={{ readOnly: true }}
                  sx={{ backgroundColor: '#f3f4f6' }}
                />
              )}
            </Box>
          </Box>
        ))}
      </Box>
    );
  }

  return (
    <TextField
      variant="outlined"
      value={typeof value === 'object' ? JSON.stringify(value, null, 2) : value ?? ''}
      fullWidth
      multiline
      minRows={1}
      size="small"
      InputProps={{ readOnly: true }}
      sx={{ backgroundColor: '#f3f4f6', mb: 1 }}
    />
  );
};

const DynamicJsonRenderer: React.FC<DynamicJsonRendererProps> = ({ data }) => {
  return (
    <Paper sx={{ padding: 3 }}>
      {Object.entries(data).map(([key, value], index) => (
        <Box key={index} sx={{ mb: 3 }}>
          <Typography variant="subtitle1" sx={{ fontWeight: 600, mb: 1 }}>
            {formatKey(key)}
          </Typography>
          {renderNested(value, 0)}
        </Box>
      ))}
    </Paper>
  );
};

export default DynamicJsonRenderer;
