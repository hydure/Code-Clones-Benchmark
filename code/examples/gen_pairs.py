from __future__ import print_function
import sys
import itertools

sys.argv.pop(0)
while len(sys.argv) > 0:
    clones=[]
    frags = int(sys.argv.pop(0))
    sim = int(sys.argv.pop(0))

    for i in range(frags):
        clone=[]
        clone.append(sys.argv.pop(0))
        clone.append(sys.argv.pop(0))
        clone.append(sys.argv.pop(0))
        clones.append(clone)

    clone_pairs=list(itertools.combinations(clones, 2))
    for pair in clone_pairs:
        print(" ".join(pair[0]), end=" ")
        print(" ".join(pair[1]), end=" ")
        print(sim, end=" ")
