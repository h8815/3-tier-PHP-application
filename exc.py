import os

# 1. Automatically find the folder where THIS script is currently saved
SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))

# 2. Set the output file path to be in that same folder
OUTPUT_FILE = os.path.join(SCRIPT_DIR, "output.txt")

def extract_files(root_path):
    with open(OUTPUT_FILE, "w", encoding="utf-8") as output:
        for root, _, files in os.walk(root_path):
            for file in files:
                file_path = os.path.join(root, file)
                
                # IMPORTANT: Skip the script itself and its output file
                if file_path == os.path.abspath(__file__) or file_path == os.path.abspath(OUTPUT_FILE):
                    continue

                output.write(f"\n=== FILE: {file_path} ===\n")

                try:
                    with open(file_path, "r", encoding="utf-8") as f:
                        output.write(f.read() + "\n")
                except UnicodeDecodeError:
                    output.write("[SKIPPED] Binary file or unsupported encoding.\n")
                except Exception as e:
                    output.write(f"[ERROR] {e}\n")

if __name__ == "__main__":
    # Use SCRIPT_DIR as the root so it only scans the folder it is in
    extract_files(SCRIPT_DIR)
    print(f"Done. Collected data from: {SCRIPT_DIR}")
    print(f"Output saved to: {OUTPUT_FILE}")
