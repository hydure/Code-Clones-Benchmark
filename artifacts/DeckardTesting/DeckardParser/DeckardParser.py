#  Script used for parsing Deckard output in multiple files.
#  Will require some additional parsing to prepare for Sprint 4.
#  At the moment, this script simply pulls the necessary information from the
#  output Deckard produces and maintains the clustering, also identifying the
#  line numbers of each clone.
#  In order to make entry into databases easier, clusters will be divided into
#  pairs with each other entry in each cluster. During this operation,
#  clones will be assigned to "fileA" and "fileB," as required by our database
#  structure.
#  Furthermore, parameterization of Deckard will require a specific directory
#  setup server-side and a script that will alter the config file necessary
#  for running Deckard.This script may eventually include methods to create the
#  directory structure for Deckard. Aforementioned issues are planned for
#  completion by Sprint 4.

from collections import defaultdict
import os
import glob

def read_file():
    os.chdir(os.getcwd()+"/clusters")
    for filename in os.listdir(os.getcwd()):
        print("*" * 50)
        print(filename)
        print("*" * 50+"\n\n\n")        
#        print(os.getcwd())
        text_file = open(filename)
        for line in text_file:
            if line == "\n":
                print("\n\n********Clone Pair/Cluster*******\n")
            for word in line.split():
                if "LINE" in word:
                    lines = word
                    lines_split = lines.split(":")
                    clone_start_line = lines_split[1]
                    clone_end_line = int(clone_start_line) + int(lines_split[2])
#                    print("Start: ", clone_start_line)
#                    print("End:   ", clone_end_line)
                if "/" in word:
                    files = word
                    fileNames = files.split("/")
                    print(fileNames[len(fileNames)-1])
        print("*" * 50 + "\n\n\n")
        text_file.close()

if __name__ == "__main__":
    read_file()
