<?php
$csvFile = "contacts.csv";
header("Content-Type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<CiscoIPPhoneDirectory>
  <Title>Thorn Family Directory</Title>
  <Prompt>Select a contact</Prompt>
<?php
$rows = array_map("str_getcsv", file($csvFile));
foreach ($rows as $index => $row) {
    if ($index === 0) continue; // skip header
    echo "  <DirectoryEntry>\n";
    echo "    <Name>" . htmlspecialchars($row[0]) . "</Name>\n";
    echo "    <Telephone>" . htmlspecialchars($row[1]) . "</Telephone>\n";
    echo "  </DirectoryEntry>\n";
}
?>
</CiscoIPPhoneDirectory>

